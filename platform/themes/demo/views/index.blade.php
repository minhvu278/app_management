{!! Theme::partial('header') !!}
<section class="banner bg-1" id="home">

    <div class="container">
        <div class="row">
            <div class="col-md-8 align-self-center">
                <!-- Contents -->
                <div class="content-block">
                    <h1>Tân Đệ Portal</h1>
                    @if (env('FILEAPPLE_PASSWORD'))
                    <h5 class="mb-4">Nhập mật khẩu để tải ứng dụng</h5>
                    @endif
                    <!-- App Badge -->
                    <div class="app-badge">
                        @if (env('FILEAPPLE_PASSWORD'))
                        <div id="input">
                            <form action="" id="form_checkpass">

                                <div class="col-6 pl-0">
                                    <div class="d-flex">
                                        <input type="text" id="pass_dow" name="password" class="form-control">
                                        <input type="submit" id="submit_checkpass" value="Get link"
                                            class="btn btn-primary ml-1">
                                    </div>
                                    <span class="error_password"></span>
                                </div>
                            </form>

                        </div>
                        <ul class="list-inline" id="list-inline">
                        </ul>

                        @else
                        <ul class="list-inline" id="list-inline">
                            <li class="list-inline-item">
                                <a class="btn-update" href="{{env('LINKDOW_ANDROID')}}"><img class="img-fluid"
                                        src="{{Theme::asset()->url('images/app-badge/google-play.png')}}"></a>
                            </li>
                            <li class="list-inline-item">
                                <a class="btn-update" href="{{env('LINKDOW_IOS')}}/?code={{$code->code}}"><img
                                        class="img-fluid"
                                        src="{{Theme::asset()->url('images/app-badge/app-store.png')}}"></a>
                            </li>
                        </ul>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- App Image -->
                <div class="image-block">
                    <img class="img-fluid" src="{{Theme::asset()->url('images/phones/iphone-banner.png')}}"
                        alt="iphone-banner">
                </div>
            </div>
        </div>
    </div>
    <div id="loading">
        <div class="bg-beforesend">
            <img class="w-100" src="{{Theme::asset()->url('images/unnamed.gif')}}" alt="">
        </div>
    </div>
</section>
{!! Theme::partial('footer') !!}