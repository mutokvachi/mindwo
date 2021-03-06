<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>MEDUS</title>
    <style>
	/* -------------------------------------
		GLOBAL
		A very basic CSS reset
	------------------------------------- */
	* {
		margin: 0;
		padding: 0;
		font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
		box-sizing: border-box;
		font-size: 14px;
	}

	img {
		max-width: 100%;
	}

	body {
		-webkit-font-smoothing: antialiased;
		-webkit-text-size-adjust: none;
		width: 100% !important;
		height: 100%;
		line-height: 1.6;
	}

	/* Let's make sure all tables have defaults */
	table td {
		vertical-align: top;
	}

	/* -------------------------------------
		BODY & CONTAINER
	------------------------------------- */
	body {
		background-color: #f6f6f6;
	}

	.body-wrap {
		background-color: #f6f6f6;
		width: 100%;
	}

	.container {
		display: block !important;
		max-width: 600px !important;
		margin: 0 auto !important;
		/* makes it centered */
		clear: both !important;
	}

	.content {
		max-width: 600px;
		margin: 0 auto;
		display: block;
		padding: 20px;
	}

	/* -------------------------------------
		HEADER, FOOTER, MAIN
	------------------------------------- */
	.main {
		background: #fff;
		border: 1px solid #e9e9e9;
		border-radius: 3px;
	}

	.content-wrap {
		padding: 20px;
	}

	.content-block {
		padding: 0 0 20px;
	}

	.header {
		width: 100%;
		margin-bottom: 20px;
	}

	.footer {
		width: 100%;
		clear: both;
		color: #999;
		padding: 20px;
	}
	.footer a {
		color: #999;
	}
	.footer p, .footer a, .footer unsubscribe, .footer td {
		font-size: 12px;
	}

	/* -------------------------------------
		TYPOGRAPHY
	------------------------------------- */
	h1, h2, h3 {
		font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
		color: #000;
		margin: 40px 0 0;
		line-height: 1.2;
		font-weight: 400;
	}

	h1 {
		font-size: 32px;
		font-weight: 500;
	}

	h2 {
		font-size: 24px;
	}

	h3 {
		font-size: 18px;
	}

	h4 {
		font-size: 14px;
		font-weight: 600;
	}

	p, ul, ol {
		margin-bottom: 10px;
		font-weight: normal;
	}
	p li, ul li, ol li {
		margin-left: 5px;
		list-style-position: inside;
	}

	/* -------------------------------------
		LINKS & BUTTONS
	------------------------------------- */
	a {
		color: #1ab394;
		text-decoration: underline;
	}

	.btn-primary {
		text-decoration: none;
		color: #FFF;
		background-color: #2D5F8B;
		border: solid #2D5F8B;
		border-width: 5px 10px;
		line-height: 2;
		font-weight: bold;
		text-align: center;
		cursor: pointer;
		display: inline-block;
		border-radius: 5px;
	}
	
	.header-title {
			text-decoration: none;
			color: #FFF;
			background-color: #F4D03F;
			border: solid #F4D03F;
			border-width: 5px 10px;
			font-weight: bold;
			text-align: center;
			display: inline-block;
			border-radius: 5px;
			text-transform: capitalize;
			height: 100px;
			width: 100%;
			font-size: 40px;
			padding-top: 30px;
	}
	
	.table-task {
		border: 1px solid #ddd;
		text-align: left;
		border-collapse: collapse;
		width: 100%;
	}
	
	.table-task td {
		padding: 15px;
		border: 1px solid #ddd;
		text-align: left;
	}

	/* -------------------------------------
		OTHER STYLES THAT MIGHT BE USEFUL
	------------------------------------- */
	.last {
		margin-bottom: 0;
	}

	.first {
		margin-top: 0;
	}

	.aligncenter {
		text-align: center;
	}

	.alignright {
		text-align: right;
	}

	.alignleft {
		text-align: left;
	}

	.clear {
		clear: both;
	}
        
        .td-medus {
            padding-right: 20px;
        }

	/* -------------------------------------
		RESPONSIVE AND MOBILE FRIENDLY STYLES
	------------------------------------- */
	@media only screen and (max-width: 640px) {
		h1, h2, h3, h4 {
			font-weight: 600 !important;
			margin: 20px 0 5px !important;
		}

		h1 {
			font-size: 22px !important;
		}

		h2 {
			font-size: 18px !important;
		}

		h3 {
			font-size: 16px !important;
		}

		.container {
			width: 100% !important;
		}

		.content, .content-wrap {
			padding: 10px !important;
		}
	}
	</style>
</head>

<body>

<table class="body-wrap">
    <tr>
        <td></td>
        <td class="container" width="600">
            <div class="content">
                <table class="main" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="content-wrap">
                            <table  cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="td-medus">
                                        <div class="header-title">MEDUS</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        <h3>Jauns uzdevums</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        MEDUS sistēmā Jums ir izveidots jauns uzdevums:
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        <table class="table-task">                
                                                <tbody>
                                                        <tr>
                                                                <td>
                                                                        Uzdevums
                                                                </td>
                                                                <td>
                                                                        <b>{{ $task_details }}</b>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <td>
                                                                        Uzdevējs
                                                                </td>
                                                                <td>
                                                                        {{ $assigner }}
                                                                </td>
                                                        </tr>

                                                        @if ($due_date)
                                                                <tr>
                                                                        <td>
                                                                                Izpildes termiņš
                                                                        </td>
                                                                        <td>
                                                                                <b><font color="red">{{ short_date($due_date) }}</font></b>
                                                                        </td>
                                                                </tr>
                                                        @endif

                                                        <tr>
                                                                <td>
                                                                        Dokumenta reģistrs
                                                                </td>
                                                                <td>
                                                                        {{ $list_title }}
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <td>
                                                                        Dokumenta ID
                                                                </td>
                                                                <td>
                                                                        {{ $doc_id }}
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <td>
                                                                        Dokumenta saturs
                                                                </td>
                                                                <td>
                                                                        {{ $doc_about }}
                                                                </td>
                                                        </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block aligncenter">
                                        <a href="{{ $portal_url }}skats_aktualie_uzdevumi?open_item_id={{ $task_id }}" class="btn-primary">Atvērt uzdevumu</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        Ja Jums neizdodas atvērt uzdevumu ar pogas nospiešanu, tad nokopējiet un ievadiet interneta pārlūkā šo adresi:<br /><br />
										{{ $portal_url }}skats_aktualie_uzdevumi?open_item_id={{ $task_id }}
                                    </td>
                                </tr>
				<tr>
                                    <td class="content-block">
                                        Informācija par uzdevumu tika nosūtīta uz e-pastu {{ $email }}. Ja šo e-pastu saņēmāt kļūdas pēc, lūdzu, informējiet par to uzņēmuma IT personālu.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block aligncenter">
                                        <font color="gray">E-pasts izsūtīts no MEDUS sistēmas {{ long_date($date_now) }}.</font>
                                    </td>
                                </tr>
                              </table>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table>

</body>
</html>
