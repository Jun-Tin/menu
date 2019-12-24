<?php 
return [
    'paypal' => [
        /** set your paypal credential **/
        'client_id' =>'paypal client_id',
        'secret' => 'paypal secret ID',
        /**
         * SDK 配置
         */
        'settings' => array(
            /**
             * 沙盒测试'sandbox' 或者 'live'
             */
               'mode' => 'sandbox',
            /**
             * 请求超时时间
             */
            'http.ConnectionTimeOut' => 1000,
            /**
             * 是否开启日志：true开启，false不开启
             */
            'log.LogEnabled' => true,
            /**
             * 日志存储的文件
             */
            'log.FileName' => storage_path() . '/logs/paypal.log',
            /**
             * 日志级别 'DEBUG', 'INFO', 'WARN' or 'ERROR'
                *
             */
            'log.LogLevel' => 'INFO'
        ),
    ],
    
    '2checkout' => [
        //
    ]
];
 ?>