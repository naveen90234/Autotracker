@include('email.email_header')
<table align="center" cellpadding="0" cellspacing="0" class="inner-body" style="font-family:Avenir, Helvetica, sans-serif;background-color:#FFFFFF;margin:0 auto;padding:0;width:570px;" width="570">
	<tbody>
		<tr>
			<td class="content-cell" style="font-family:Avenir, Helvetica, sans-serif;padding:35px;">
				{!! $data !!}
            <p style="font-family:Avenir, Helvetica, sans-serif;color:#000;font-size:16px;line-height:1.5em;margin-top:20px;text-align:left;">Regards,  <br>{{ env('APP_NAME') }}<br />
            </p>
            </td>
        </tr>
    </tbody>
</table>
@include('email.email_footer')
