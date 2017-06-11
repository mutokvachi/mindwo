<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>MEDUS - notifikācija par uzdotu jautājumu</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body style="margin: 0; padding: 0;">
        <p>Labdien!</p>
        <p>Portālā ir reģistrēts jauns jautājums:</p>
        <div style="margin-bottom: 10px; margin-left: 5px; margin-right: 5px;">
            <table border="1" cellpadding="5" cellspacing="0">                
                <tbody>
                    <tr>
                        <td>
                            Jautājums
                        </td>
                        <td>
                            {{ $question }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Reģistrēšanas laiks
                        </td>
                        <td>
                            {{ long_date($reg_time) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Nepieciešama atbilde?
                        </td>
                        <td>
                            @if ($email)
                                Jā
                            @else
                                Nē
                            @endif
                        </td>
                    </tr>
                    
                    @if ($email)
                        <tr>
                            <td>
                                E-pasts
                            </td>
                            <td>
                                <a href="mailto: {{ $email }}">{{ $email }}</a>
                            </td>
                        </tr>
                    @endif
            </table>            
        </div>
        <p>Jautājums ir saglabāts MEDUS reģistrā <a href="{{ Request::root() }}/skats_204">Iesūtītie jautājumi</a>.</p>
        <br />
        <p>E-pasts izsūtīts no MEDUS sistēmas</p>
    </body>
</html>
