<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/livelynk.js') }}" defer></script>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <!-- Styles -->
        <style>
            html, body {
                font-family:-apple-system, BlinkMacSystemFont, "Helvetica Neue", "Segoe UI","Noto Sans Japanese","ヒラギノ角ゴ ProN W3", Meiryo, sans-serif;
                background-color: #f1f8e9;
                color: #636b6f;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                margin-top: 200px;
                font-family: 'Raleway', sans-serif;
                font-size: 84px;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .message {
                width: 100%;
                font-size: 14px;
            }
            .summary-wp {
                background-color: #ffffff;
                margin-top: 50px;
                margin-bottom: 50px;
                margin-left: auto;
                margin-right: auto;
                padding-top: 20px;
                padding-bottom: 40px;
                width: 95%;
                border-radius: 30px;
            }
            .summary {
                text-align: left;
                margin-left: auto;
                margin-right: auto;
                width: 90%;
            }
            h1 {
                margin-top: 30px;
                margin-bottom: 40px;
                font-size: 40px;
            }
            h2 {
                margin-bottom: 30px;
                font-size: 30px;
            }
            h3 {
                margin-top: 50px;
                margin-bottom: 10px;
                font-size: 26px;
            }
            p {
                font-size: 18px;
                padding-left: 5%;

            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref">
            <div class="content">
                <div class="title m-b-md">
                    <div class="message">
                        @component('components.message')
                        @endcomponent
                    </div>
                    {{ config('app.name', 'Laravel') }}<br>@ Geek Office
                </div>
                <p class="lead">会員の方は専用のURLより閲覧してください。</p>
                <div class="summary-wp">
                    <div class="summary">
                        <h1>Livelynkとは?</h1>
                        <h2>今その『場』に『誰』がいるか？　がわかるウェブサービスです</h2>

                        <h3>『場』の持つ力</h3>
                        <p>シェアオフィスやコワーキングスペース、趣味のコミュニティ等、シェアリングエコノミーやコミュニティが価値を持つ時代になりました。昔と比べて、好きな時間に好きな場所で好きな人同士で好きな事をできる時代になりつつある事を実感する人は多いのではないでしょうか？</p>
                        <p>『場』が盛り上がる事で単に楽しいだけでなく、新しい繋がりや、関係、モノが生まれる可能性を秘めた時代に来ていると思いませんか？</p>

                        <h3>『場』が盛り上がらないのは何故？</h3>
                        <p>でも、あなたか集うそのリアルな『場』っていつも盛り上がってますか？
                        実は『場』で今一つ足りないものは、求心力だったりしませんか？任意の人が任意の時間に集うユルいコミュニティでは、どうしても時間的なすれ違いが発生しがちです。かといって、毎回時間を決めて集まるというのも少し違う気がします。また、コミュニティの中心人物がとても頑張って場に集まる機会を作り盛り上げていかなければいけません。しかし、もっと自由で、気楽に、『場』の持つ求心力を上げる方法はないのでしょうか？</p>

                        <h3>『誰か』をきっかけに集い『何か』が生まれる世界を</h3>
                        <p>そんなニーズに答える仕組みがとあるシェアオフィスで生まれました。5年以上のコミュニティのノウハウを集約させ、人々が気楽に集い、新しい何かを生み出す仕組みを企画し、製作しました。それがLivelynk（ライブリンク）です。</p>
                        <p>Livelynkとは　Lively（活発な・賑やかな）  と Link（関連・きずな）  を掛け合わせた造語です。</p>

                        <h3>しくみ</h3>
                        <p>小さくて安価なコンピュータ”RaspberryPi”で『場』のwi-fiネットワークに繋がったスマホやパソコンの固有ID（MACアドレス）を調べて、持ち主である『誰』が居るかをウェブページやスマホアプリ等でお知らせする仕組みです。</p>
                        <p>『場』のメンバーだけが知っている非公開ウェブサイトで『今誰がいる』かを閲覧したり、SlackやLINE等のチャットアプリと連携して、来訪者や帰宅者の通知をスマホやパソコンでリアルタイムに知る事が出来ます。もちろん非通知にして存在を隠すことも可能です。</p>
                        <p>webカメラを設置して監視社会的になったり、プライバシーを侵害したりすることなく、コミュニティのメンバーだけが、その『場』の人の存在を伺い知る事ができます。</p>

                        <h3>作ったのは？</h3>
                        <p>ギークオフィス恵比寿というシェアオフィスの会員で40歳を過ぎてからプログラミングを始めたIT講師です。</p>
                        <p>シェアオフィスで「今誰が居るかを知りたい」という要望を元に、ほぼ一人で一月かけて開発しました。（企画やデザインは共同作業です）</p>
                        <p>既にギークオフィス恵比寿では多くのメンバーに使ってもらっています。また現在は多くのコミュニティで使って頂けるよう改修作業に勤しんでします。</p>
                        <p>人生に絶望して周囲の人に迷惑をかけ続けたため今はとても貧乏なので、よかったら買ってください。</p>

                        <h3>価格は？</h3>
                        <p>本体RasberryPi 及びソフト一式で2万円～（税別）<br>
                            また月額利用費が1千円～（税別 登録数に応じて変更）<br>
                            ですがもし、この仕組みにより価値を感じて頂いたら、より多くの価格で購入して欲しいと思っています。</p>

                        <h3>購入方法は？</h3>
                        <p>以下のメールアドレスより受け付けています。<br>
                        <a href="mailto:livelynk.jp@gmail.com?subject=Livelynk購入のお問い合わせ">livelynk.jp@gmail.com</a>
                        </p>
                        <p>※端末のお届けとご利用開始は、2018年12月以降を予定してします。</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
