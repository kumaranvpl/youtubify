<!DOCTYPE html>
<html>
    <head>
        {{-- Set base url if admin has enabled HTML5 push state --}}
        @if ($settings->get('enablePushState'))
            <base href="{{ $pushStateRootUrl }}">
        @endif

        <title>{{ $settings->get('siteName') }}</title>

        {{-- Meta --}}
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="fragment" content="!">
        <meta name="google" content="notranslate">

        @if ($settings->get('metaTitle'))
            <meta name="title" content="{{ $settings->get('metaTitle') }}" />
        @endif

        @if ($settings->get('metaDescription'))
            <meta name="description" content="{{ $settings->get('metaDescription') }}" />
        @endif

        {{-- CSS For Splash Spinner --}}
        <style>
            #splash,[ng-cloak]{display:none}#splash,.inner{position:absolute}[ng-cloak]#splash{display:flex!important}#splash{top:0;left:0;width:100%;height:100%;z-index:9999;justify-content:center;align-items:center}#splash-spinner{display:block!important;width:120px;height:120px;border-radius:50%;perspective:800px}.inner{box-sizing:border-box;width:100%;height:100%;border-radius:50%}.inner.one{left:0;top:0;animation:rotate-one 1s linear infinite;border-bottom:3px solid #84BD00}.inner.two{right:0;top:0;animation:rotate-two 1s linear infinite;border-right:3px solid #84BD00}.inner.three{right:0;bottom:0;animation:rotate-three 1s linear infinite;border-top:3px solid #84BD00}@keyframes rotate-one{0%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(0)}100%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@keyframes rotate-two{0%{transform:rotateX(50deg) rotateY(10deg) rotateZ(0)}100%{transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@keyframes rotate-three{0%{transform:rotateX(35deg) rotateY(55deg) rotateZ(0)}100%{transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}
        </style>

        {{-- CSS --}}
        <link rel="stylesheet" id="main-stylesheet"
              href="{{ asset('assets/css/custom-stylesheets', $settings->get('enable_https')).'/'.(($settings->get('selected_sheet') && ! IS_DEMO) ? $settings->get('selected_sheet').'/styles.min.css?v16' : 'original/styles.min.css?v16') }}">

        {{-- Fonts --}}
        <link href='https://fonts.googleapis.com/css?family=RobotoDraft:300,400,500,600,700' rel='stylesheet' type='text/css'>

        {{-- Favicons --}}
        <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="favicons/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="favicons/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
        <link rel="manifest" href="favicons/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="favicons/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
    </head>

    <body ng-app="app" global-dropdown-close ng-controller="AppController">

        <div id="splash" ng-cloak>
            <div id="splash-spinner">
                <div class="inner one"></div>
                <div class="inner two"></div>
                <div class="inner three"></div>
            </div>
        </div>

        <div ui-view="full" id="full-page-view"></div>

        <div class="app-container" ng-class="{ visible: !shouldPlayerControlsBeHidden() }">
            <section class="page-container" ng-cloak>
                <left-panel></left-panel>
                <search-panel></search-panel>

                <div class="middle-view">
                    <nav class="mobile-nav" ng-class="{ show: isPhone }">
                        <div class="toggle-icon" sidebar-toggler><i class="icon icon-menu"></i></div>
                        <div class="state-name">@{{ getCurrentStateName() }}</div>
                        <div class="mobile-search-bar">
                            <i class="icon icon-search"></i>
                            <input type="text" class="mobile-search-bar" ng-model="search.query" ng-model-options="{ debounce: 400 }" ng-change="search.getResults(search.query)">
                            <i class="icon icon-spin6 spin search-bar-spinner" ng-if="search.ajaxInProgress"></i>
                        </div>
                    </nav>
                    <div class="ad-container" ng-if="ad1" ng-bind-html="ad1"></div>

                    <div ui-view id="main-view" ng-class="{ 'has-advert': ad1 || ad2, 'has-multiple-adverts': ad1 && ad2 }"></div>

                    <div class="ad-container" ng-if="ad2" ng-bind-html="ad2"></div>
                </div>

                <right-panel></right-panel>

                <div class="lyrics-container hidden" lyrics-container>
                    <div class="backdrop"></div>
                    <div class="modal-inner-container">
                        <div id="lyrics-panel" class="scroll-container" pretty-scrollbar></div>
                        <div class="close-lyrics-icon"><i class="icon icon-cancel"></i></div>
                    </div>
                </div>

                <div class="player-container hidden" video-container ng-class="{ show: utils.getSetting('show_youtube_player') }">
                    <div class="modal-inner-container">
                        <div id="player"></div>
                        <div class="yt-overlay"></div>
                        <div class="close-lyrics-icon"><i class="icon icon-cancel"></i></div>
                        <div class="toggle-fullscreen" ng-if="utils.getSetting('show_fullscreen_button')"><i class="icon icon-resize-full"></i></div>
                    </div>
                    <div class="backdrop"></div>
                </div>

                <div id="context-menu" class="hidden" context-menu ng-controller="ContextMenuController">
                    <div class="header">
                        <div class="image"></div>
                        <div class="info">
                            <div class="name"></div>
                            <div class="sub-name"></div>
                        </div>
                    </div>
                    <div class="body">
                        <add-to-playlist class="slide-out"></add-to-playlist>
                    </div>
                </div>
            </section>

            <player-controls></player-controls>
        </div>

        <script id="vars">
            var vars = {
                user: '{!! $user !!}',
                baseUrl: '{{ $baseUrl }}',
                selectedLocale: '{{ Config::get('app.locale') }}',
                trans: {!! $translations !!},
                settings: {!! json_encode($settings->getAll()) !!},
                isDemo: '{{ $isDemo  }}'
            }
        </script>

        <script src="{{ asset('assets/js/core.min.js?v16', $settings->get('enable_https')) }}"></script>

        @if (($locale = $settings->get('dateLocale', 'en')) && $locale !== 'en')
            <script src="{{ asset('assets/js/locales/'.$locale.'.js')  }}"></script>
        @endif

        @if ($code = $settings->get('analytics'))
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', '{{ $settings->get('analytics') }}', 'auto');
                ga('send', 'pageview');
            </script>
        @endif
    </body>
</html>
