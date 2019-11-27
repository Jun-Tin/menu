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
    const clientId = 'AaI3OTYDtSmZ9-KkCVecwKWq5GKmp8s_SyTcEbRiWHBEFYjT3ID2nzHokcKaE5KBDeRX0WzRksgNQahE';//ID
    const clientSecret = 'EJe3inSl5Kig3pnlSl2mPh_WtiwsytD3hPUrjfyV0P-ZYpYi81UmhbsClZgcMfs9CHIzmDHOAd4oAw9V';//秘钥
    const accept_url = 'http://menu.test/api/paypal/callback';//返回地址
    const Currency = 'USD';//币种
    protected $PayPal;

    // sb-1qjqf536347@business.example.com
    // iP{_D-K7

    public function __construct()
    {
        $this->PayPal = new ApiContext(
            new OAuthTokenCredential(
                self::clientId,
                self::clientSecret
            )
        );
        //如果是沙盒测试环境不设置，请注释掉
//        $this->PayPal->setConfig(
//            array(
//                'mode' => 'live',
//            )
//        );
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
            echo $e->getData();
            die();
        }

        $approvalUrl = $payment->getApprovalLink();
        dd($approvalUrl);
        header("Location: {$approvalUrl}");
    }

    /**
     * 同步回调
     */
    public function callback()
    {
        $success = trim($_GET['success']);

        if ($success == 'false' && !isset($_GET['paymentId']) && !isset($_GET['PayerID'])) {
            echo '取消付款';die;
        }

        $paymentId = trim($_GET['paymentId']);
        $PayerID = trim($_GET['PayerID']);

        if (!isset($success, $paymentId, $PayerID)) {
            echo '支付失败';die;
        }

        if ((bool)$_GET['success'] === 'false') {
            echo  '支付失败，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';die;
        }

        $payment = Payment::get($paymentId, $this->PayPal);

        $execute = new PaymentExecution();

        $execute->setPayerId($PayerID);

        try {
            $payment->execute($execute, $this->PayPal);
        } catch (Exception $e) {
            echo ',支付失败，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';die;
        }
        echo '支付成功，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';die;
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
