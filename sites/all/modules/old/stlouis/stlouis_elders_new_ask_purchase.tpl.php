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

require_once("paypal.inc");

// Put together the data we're going to send to PayPal.

$nvps = array();

// According to the sources I've talked to internally,
// this HAS to be set to 65.1.  No other version is
// currently supported.
$nvps["VERSION"] = "65.1";

// Since this is a special-purpose script that is designed
// to handle a single item purchase, I can get away with
// hardcoding most of my values.  In practice, this isn't
// such a good idea, but for a demo, it'll work.
$nvps["METHOD"] = "SetExpressCheckout";
$nvps["RETURNURL"] = "http://stl2114.game.ziquid.com/stlouis/home/abc123";
$nvps["CANCELURL"] = "http://stl2114.game.ziquid.com/stlouis/home/abc123";
$nvps["PAYMENTREQUEST_0_AMT"] = "1.99";
$nvps["PAYMENTREQUEST_0_CURRENCYCODE"] = "USD";
$nvps["PAYMENTREQUEST_0_ITEMAMT"] = "1.99";
$nvps["L_PAYMENTREQUEST_0_NAME0"] = "10 Luck";
$nvps["L_PAYMENTREQUEST_0_DESC0"] = "10 Luck for StLouis 2114";
$nvps["L_PAYMENTREQUEST_0_AMT0"] = "1.99";
$nvps["L_PAYMENTREQUEST_0_QTY0"] = "1";

// This is what makes the whole magic happen --
// don't forget this one!
$nvps["L_PAYMENTREQUEST_0_ITEMCATEGORY0"] = "Digital";

// Since it's a digital good (and not physical),
// we don't need a shipping address.
$nvps["REQCONFIRMSHIPPING"] = "0";
$nvps["NOSHIPPING"] = "1";

// Send the API call to PayPal.
$response = RunAPICall($nvps);

// Did we get an error back from PayPal?  Did PayPal
// not give us a token?  If so, fail now.

if (($response["ACK"] != 'Success' && $response["ACK"] != "SuccessWithWarning")
 || !strlen($response["TOKEN"]))
	PaymentError();
	
// Otherwise, grab our token and redirect the buyer to PayPal.

header("Location: https://www.sandbox.paypal.com/incontext?token=" .
  $response["TOKEN"]);

// And that's it!  That's all this script needs to do.
