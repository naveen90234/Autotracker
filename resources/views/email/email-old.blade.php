<div style=' width:700px; margin:0 auto'>
    <div style='background:#fff; border:#888 solid 1px; border-radius:15px; margin-top:20px; padding:20px; box-shadow:0 0 8px #999'>
        <div style='font-size: 16px ; font-style: italic ; background-color: #fff; border-bottom: 1px solid #ccc ; padding-bottom: 0px;text-align: center;padding: 10px 0;border-radius: 12px;'>
            @if(setting('site_logo') && file_exists(setting('site_logo')))
                            <img style="max-width: 100%;height: 120px;border-radius: 10px;" src="{{url(setting('site_logo'))}}">
                            @endif
            
        </div>
        <div style='font-size:14px; padding-top:20px; word-break: break-word;'>
            {!! $data !!}
        </div>
    </div>
</div>