<?php

// Copyright (c) 2011 PayPal, Inc.

/*
/////////////////////////////////// DISCLAIMER ///////////////////////////////////

THIS EXAMPLE CODE IS PROVIDED TO YOU ONLY ON AN "AS IS" BASIS WITHOUT
WARRANTIES OR CONDITIONS OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT
LIMITATION ANY WARRANTIES OR CONDITIONS OF TITLE, NON-INFRINGEMENT,
MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE. PAYPAL MAKES NO WARRANTY THAT
THE SOFTWARE OR DOCUMENTATION WILL BE ERROR-FREE. IN NO EVENT SHALL THE COPYRIGHT
OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

// Storing your credentials hardcoded in a file isn't as secure
// as, say, storing them in a database, but it's more secure
// than including them straight in your main source code file.

define("API_USERNAME", "****");
define("API_PASSWORD", "****");
define("API_SIGNATURE", "****");

// A few utility functions to help us work with the PayPal NVP APIs.

function NVPEncode($nvps) {
	$out = array();
	foreach($nvps as $index => $value) {
		$out[] = $index . "=" . urlencode($value);
	}
	
	return implode("&", $out);
}

function NVPDecode($nvp) {
	$split = explode("&", $nvp);
	$out = array();
	foreach($split as $value) {
		$sub = explode("=", $value);
		$out[$sub[0]] = urldecode($sub[1]);
	}
	
	return $out;
}

function RunAPICall($nvps) {
	$ch = curl_init("https://api-3t.sandbox.paypal.com/nvp");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
	// On some servers, these two options are necessary to
	// avoid getting "invalid SSL certificate" errors
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	
	// Insert the credentials
	$nvps["USER"] = API_USERNAME;
	$nvps["PWD"] = API_PASSWORD;
	$nvps["SIGNATURE"] = API_SIGNATURE;
	
	// Build the NVP string
	$nvpstr = NVPEncode($nvps);
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr);
	
	$result = curl_exec($ch);
	
	// If the request failed, return an empty array.
	if($result === FALSE) return array();
	
	// Return the decoded response
	else return NVPDecode($result);
}

// I found myself using this bit of code multiple times,
// so I figured it would be good to put it in its own
// function.

function PaymentError() {
	die("Uh oh, an error occurred...sorry, I can't process your purchase " .
		"right now.  Please try again later.<br /><br />" .
		"<a href=\"ECCancel.php\">Click here</a> to close this window.");
}

?>
