<?php 
namespace App\Utils;

use Illuminate\Http\Request;
use SmsManager;

trait SmsCodeUtil
{

	public function sendSmsCode(Request $request)
	{
		/*$result = SmsManager::validateSendable();
		if(!$result['success']) {
			// return respondUnprocessable($result['message']);
		}
		$result = SmsManager::validateFields();
		if(!$result['success']) {
			// return respondUnprocessable($result['message']);
		}*/
		$result = SmsManager::requestVerifySms();
		dd($result);
		if(!$result['success']) {
			// return respondUnprocessable($result['message']);
		}
		return respondSuccess($result['message']);
	}

	public function validateSmsCode()
	{
		//验证数据
		$validator = Validator::make(inputAll(), [
			'phone_number' => 'required|confirm_mobile_not_change|confirm_rule:mobile_required',
			'sms_code'  => 'required|verify_code',
		]);

		if ($validator->fails()) {
			//验证失败后建议清空存储的发送状态，防止用户重复试错
			SmsManager::forgetState();
			respondUnprocessable(formatValidationErrors($validator));
		}
	}
}