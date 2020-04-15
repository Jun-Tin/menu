<?php

namespace App\Http\Controllers\Api;

use PayPal\Api\{Payer, Item, ItemList, Details, Amount, Transaction, RedirectUrls, Payment, PaymentExecution};
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaypalsController extends Controller
{
    // const clientId = 'Ac3Ai2BM9Wggmbz9rI-PZ5spaLJRuUtN0-POPRbhEPnhP8sT3eCLwmKolHeXqXAUJSqRiuM6YHQi0T2Z';//ID
    // const clientSecret = 'EASXST6KC_JNk5CUVnaytLkzC4UloIY--g02tjb1iJ8ND9kS7ZAUUxK4HVBm0ImFXJs7UcTrM5OoPS5B';//秘钥
    const clientId = 'AaI3OTYDtSmZ9-KkCVecwKWq5GKmp8s_SyTcEbRiWHBEFYjT3ID2nzHokcKaE5KBDeRX0WzRksgNQahE';//ID
    const clientSecret = 'EJe3inSl5Kig3pnlSl2mPh_WtiwsytD3hPUrjfyV0P-ZYpYi81UmhbsClZgcMfs9CHIzmDHOAd4oAw9V';//秘钥
    const accept_url = 'http://47.56.146.107/menub/api/paypal/callback';//返回地址
    const Currency = 'USD';//币种
    protected $PayPal;

    // 沙盒测试账号：sb-1qjqf536347@business.example.com
    // 沙盒测试密码：iP{_D-K7

    public function __construct()
    {
        $this->PayPal = new ApiContext(
            new OAuthTokenCredential(
                self::clientId,
                self::clientSecret
            )
        );
        // 如果是沙盒测试环境不设置，请注释掉
        // $this->PayPal->setConfig(
        //    array(
        //        'mode' => 'live',
        //    )
        // );
    }

    /**
     * @param
     * $product 商品
     * $price 价钱
     * $shipping 运费
     * $description 描述内容
     */
    public function pay()
    {
        $product = '1123';
        $price = 1;
        $shipping = 0;
        $description = '1123123';
        $paypal = $this->PayPal;
        $total = $price + $shipping;//总价

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName($product)->setCurrency(self::Currency)->setQuantity(1)->setPrice($price);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $details = new Details();
        $details->setShipping($shipping)->setSubtotal($price);

        $amount = new Amount();
        $amount->setCurrency(self::Currency)->setTotal($total)->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription($description)->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(self::accept_url . '?success=true')->setCancelUrl(self::accept_url . '/?success=false');

        $payment = new Payment();
        $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions([$transaction]);
        try {
            $payment->create($paypal);
        } catch (PayPalConnectionException $e) {
            return response()->json(['data' => $e->getData(), 'status' => 401]);
            // echo $e->getData();
            // die();
        }

        $approvalUrl = $payment->getApprovalLink();
        // header("Location: {$approvalUrl}");
        return response()->json(['data' => $approvalUrl, 'status' => 200]);
    }

    /**
     * 同步回调
     */
    public function callback()
    {
        $success = trim($_GET['success']);

        if ($success == 'false' && !isset($_GET['paymentId']) && !isset($_GET['PayerID'])) {
            // $message = '取消付款';
            return redirect("http://47.56.146.107/menu_client/#/Fail");
        }

        $paymentId = trim($_GET['paymentId']);
        $PayerID = trim($_GET['PayerID']);

        if (!isset($success, $paymentId, $PayerID)) {
            // $message = '支付失败';
            return redirect("http://47.56.146.107/menu_client/#/Fail");
        }

        if ((bool)$_GET['success'] === 'false') {
            // $message = '支付失败，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';
            return redirect("http://47.56.146.107/menu_client/#/Fail?paymentId={$paymentId}&PayerID={$PayerID}");
        }

        $payment = Payment::get($paymentId, $this->PayPal);

        $execute = new PaymentExecution();

        $execute->setPayerId($PayerID);

        try {
            $payment->execute($execute, $this->PayPal);
        } catch (Exception $e) {
            // $message = '支付失败，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';
            return redirect("http://47.56.146.107/menu_client/#/Fail?paymentId={$paymentId}&PayerID={$PayerID}");
        }
        return redirect("http://47.56.146.107/menu_client/#/Success?paymentId={$paymentId}&PayerID={$PayerID}");
        // echo '支付成功，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';

        // return response()->json(['status' => 200, 'message' => $message]);
    }


    /**
     * 异步回调
     */ 
    public function notify(){

        //获取回调结果
        $json_data = $this->get_JsonData();

        if(!empty($json_data)){
            Log::debug("paypal notify info:\r\n".json_encode($json_data));
        }else{
            Log::debug("paypal notify fail:参加为空");
        }
          //自己打印$json_data的值看有那些是你业务上用到的
          //比如我用到
          $data['invoice'] = $json_data['resource']['invoice_number'];
          $data['txn_id'] = $json_data['resource']['id'];
          $data['total'] = $json_data['resource']['amount']['total'];
          $data['status'] = isset($json_data['status'])?$json_data['status']:'';
          $data['state'] = $json_data['resource']['state'];

        try {
            //处理相关业务
        } catch (\Exception $e) {
            //记录错误日志
            Log::error("paypal notify fail:".$e->getMessage());

            return "fail";
        }
        return "success";
    }

    public function get_JsonData(){
        $json = file_get_contents('php://input');
        if ($json) {
            $json = str_replace("'", '', $json);
            $json = json_decode($json,true);
        }
        return $json;
    }

    /**
     * 退款
     */
    public function returnMoney()
    {
        try {
            $txn_id = "xxxxxxx";  //异步加调中拿到的id
            $amt = new Amount();
            $amt->setCurrency('USD')
                ->setTotal('99');  // 退款的费用

            $refund = new Refund();
            $refund->setAmount($amt);

            $sale = new Sale();
            $sale->setId($txn_id);

            $refundedSale = $sale->refund($refund, $this->PayPal);
        } catch (\Exception $e) {
            // PayPal无效退款
            return json_decode(json_encode(['message' => $e->getMessage(), 'code' => $e->getCode(), 'state' => $e->getMessage()]));  // to object
        }
        // 退款完成
        return $refundedSale; 
    }
}
