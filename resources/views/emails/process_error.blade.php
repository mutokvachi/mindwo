<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>LEPORTS - procesa "{{$process_name}}" kļūda</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body style="margin: 0; padding: 0;">
        <p>Labdien!</p>
        <p>Procesā "{{$process_name}}" ir notikusi kļūda:</p>
        <div style="margin-bottom: 10px; margin-left: 5px; margin-right: 5px;">
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <td>
                            Processa izsaukšanas laiks
                        </td>
                        <td>
                            Process sākuma laiks
                        </td>
                        <td>
                            Process noslēgšanās laiks
                        </td>
                        <td>
                            Kļūdas paziņojums
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            {{$process_log_entry->register_time}}
                        </td>
                        <td>
                            {{$process_log_entry->start_time}}
                        </td>
                        <td>
                            {{$process_log_entry->end_time}}
                        </td>
                        <td>
                            {{$process_log_entry->message}}
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
        <small>E-pasts izsūtīts no LEPORTS sistēmas</small>
    </body>
</html>
