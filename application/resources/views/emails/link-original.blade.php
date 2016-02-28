<table cellpadding="0" cellspacing="0" style="border-radius:4px;border:1px #dceaf5 solid" border="0" align="center">
    <tbody>
        <tr>
            <td colspan="3" height="6"></td>
        </tr>
    <tr style="line-height:0">
        <td width="100%" style="font-size:0" align="center" height="1"><img width="40px" style="max-height:73px;width:40px" src="{{ asset('assets/images/logo_dark.png') }}"></td>
    </tr>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" style="line-height:25px" border="0" align="center">
                <tbody>
                <tr>
                    <td colspan="3" height="30"></td>
                </tr>
                <tr>
                    <td width="36"></td>
                    <td width="454" align="left" style="color:#444444;border-collapse:collapse;font-size:11pt;font-family:proxima_nova,'Open Sans','Lucida Grande','Segoe UI',Arial,Verdana,'Lucida Sans Unicode',Tahoma,'Sans Serif';max-width:454px" valign="top">
                        {{ $user->getNameOrEmail() }} {{ trans('app.justShared')  }}

                        @if($emailMessage)
                            <br><br>
                            {{ $emailMessage }}
                        @endif

                        <br><br>
                        <a href="{{ $link }}" target="_blank">{{trans('app.clickToView')}}</a>.
                    </td>
                    <td width="36"></td>
                </tr>
                <tr>
                    <td colspan="3" height="36"></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>