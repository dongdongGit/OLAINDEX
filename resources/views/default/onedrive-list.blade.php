@extends ('default.layouts.main')
@section('title', getAdminConfig('site_name'))
@section('css')
<link href="https://cdn.bootcss.com/blueimp-gallery/2.33.0/css/blueimp-gallery-indicator.min.css" rel="stylesheet">
<link href="https://cdn.bootcss.com/blueimp-gallery/2.33.0/css/blueimp-gallery.min.css" rel="stylesheet">
<link href="https://cdn.bootcss.com/normalize/8.0.1/normalize.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('css/fsbanner.css') }}">
@stop
@section('js')
<script type="text/javascript" src="{{ asset('js/fsbanner.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('.ex[name=2] .fsbanner').fsBanner({
			trigger:'mouse'
        });

        $('.fsbanner > div').hover(
            function () {
                $(this).children('.join-box').css({
                    'display': 'flex'
                })
            },
            function () {
                $(this).children('.join-box').css({
                    'display': 'none'
                })
            }
        );
    });
</script>
@stop
@section('content')
<div class="jumbotron">
    <h1 class="display-3">选择 OneDrive </h1>
    <hr class="my-4">
    <p class="lead">
        <article class="htmleaf-container">
            <div class='ex' name='2'>
                <div class='fsbanner'>
                @if ($oneDrives->isEmpty()) 
                    <h1 class="display-3">请先绑定 OneDrive 账号</h1>
                @else
                    @foreach ($oneDrives as $oneDrive)
                    <div style='background-image:url({{ $oneDrive->cover ? $oneDrive->cover->path : '' }})'>
                        <span class='name'>{{ $oneDrive->name }}</span>
                        <span class="join-box fsbanner-button clockwise fsbanner-both">
                            <a href="{{ route('home', ['onedrive' => $oneDrive->id]) }}" class="join"> 进&nbsp;&nbsp;入 </a>
                        </span>
                    </div>   
                    @endforeach
                @endif
                </div>
            </div>
        </article>
    </p>
</div>
@stop