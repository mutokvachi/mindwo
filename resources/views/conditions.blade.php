<!DOCTYPE html>
<html lang="en">
<head>
    <title>Title</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="google-site-verification" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="icon" href="/favicon.ico" type="image/png" sizes="16x16">

    <link href="https://use.fontawesome.com/releases/v5.0.7/css/all.css" rel="stylesheet">
    <style type="text/css">
        body{
            font-size:14px;
            font-family:Montserrat-light;
            width:100%;
            /*background:-webkit-linear-gradient(-30deg,#fff 50%,#74000f 50%);
            background:linear-gradient(-30deg,#fff 50%,#74000f 50%);*/
            color:#fff;
            background: #e6e6e6;
        }
        .terms-block{
            width:80%;
            box-shadow: 0px 0px 12px #ccc;
            display:-webkit-box;
            display:-moz-box;
            display:-ms-flexbox;
            display:-webkit-flex;
            display:flex;
            -webkit-flex-direction:column;
            flex-direction:column;
            -webkit-box-align:center;
            -moz-box-align:center;
            -ms-flex-align:center;
            -webkit-align-items:center;
            align-items:center;
            background-color:#fff;
            position:absolute;
            top:50%;
            left:50%;
            transform:translate3d(-50%,-50%,0);
            padding:30px
        }
        .terms-block img{
            width:215px
        }
        .terms-block .title{
            margin:25px 0;
            font-family:Montserrat-bold;
            font-size:18px;
            color:#000
        }
        .terms-block .txt-block{
            padding:10px;
            width:90%;
            text-align:center;
            max-height:350px;
            overflow-y:scroll
        }
        .terms-block .txt-block .txt{
            font-family:Montserrat-light;
            font-size:14px;
            color:#000;
            margin-bottom:15px;
            line-height:24px
        }
        .terms-block .txt-block::-webkit-scrollbar{
            width:2px
        }
        .terms-block .txt-block::-webkit-scrollbar-track{
            border-radius:10px;
            background:#eee
        }
        .terms-block .txt-block::-webkit-scrollbar-thumb{
            border-radius:10px;
            background:#999
        }
        .terms-block .txt-block::-webkit-scrollbar-thumb:window-inactive{
            background:rgba(100,100,100,.4)
        }
        .terms-block .btns{
            margin-top:50px
        }
        .terms-block .btns .btn{
            -webkit-border-radius:5px!important;
            -moz-border-radius:5px!important;
            -ms-border-radius:5px!important;
            border-radius:5px!important;
            padding:10px 30px 12px;
            margin:0 5px;
            -moz-box-shadow:none!important;
            -webkit-box-shadow:none!important;
            box-shadow:none!important;
            -webkit-appearance:none!important;
            -moz-appearance:none!important;
            appearance:none!important;
            transition:.3s
        }
        .terms-block .btns .decline{
            background-color:#b9b8b8;
            border:1px solid #b9b8b8;
            font-family:Montserrat-bold;
            font-size:14px;
            color:#fff
        }
        .terms-block .btns .decline:hover{
            background-color:#fff;
            color:#8a8989
        }
        .terms-block .btns .accept{
            border:1px solid #74000f;
            background-color:#74000f;
            font-family:Montserrat-bold;
            font-size:14px;
            color:#fff
        }
        .terms-block .btns .accept:hover{
            background-color:#fff;
            color:#74000f
        }
        @media (max-width:1024px){
            .terms-block{
                width:90%
            }
        }
        @media (max-width:575px){
            .terms-block{
                padding:30px 10px
            }
            .terms-block .txt-block{
                max-height:300px
            }
            .terms-block .btns{
                margin-top:30px
            }
        }
        @media (max-width:575px) and (max-width:320px){
            .terms-block .txt-block{
                max-height:250px
            }
        }

    </style>
</head>
<body class="site">
    <div class="terms-block">
        <a href="#!">
            <img src="/assets/global/logo/logo-medus-green.png" alt="">
        </a>
        <div class="title">Terms and Conditions</div>
        <div class="txt-block">
            <p class="txt">{!! $terms->agreement_text !!}</p>
        </div>
        <div class="d-flex btns">
            <a href="{{ route('conditionsStatus', ['status'=>'decline']) }}" class="btn decline">Decline</a>
            <a href="{{ route('conditionsStatus', ['status'=> $terms->role_id]) }}" class="btn accept">I Agree</a>
        </div>
    </div>
</body>
</html>
