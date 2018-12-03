@extends('site.layout')
@section('content')
<div class="summary">
    <h1>Livelynkとは?</h1>
    <h2>今その『場』に『誰』がいるか？ をお知らせするウェブサービスです</h2>
    <figure>
        <img src="{{asset("img/home/top_main.png")}}" alt="sample画像">
        <figcaption>※画面は合成,開発中のものです</figcaption>
    </figure>
    <h3>人が『場』に集うきっかけをサポートするシステムです</h3>
    <p>wi-fiにPCやスマホが接続されると、専用端末を通じて誰が来たかをスマホやPCにお知らします。また、会員専用サイトでは現在の滞在者一覧がわかる様になっています。</p>

    <h4 class="sp">シェアオフィス・コミュニティスペース等</h4>
    <figure>
        <img src="{{asset("img/home/system_summary.png")}}" class="pc" width="80%" alt="slack sample画像" style="border: 0px solid #fff;">

        <img src="{{asset("img/home/system_summary_1.png")}}" class="sp" width="100%" alt="slack sample画像" style="border: 0px solid #fff;">
        <img src="{{asset("img/home/system_summary_2.png")}}" class="sp" width="100%" alt="slack sample画像" style="border: 0px solid #fff;">
    </figure>

    <h3>Livelynkのしくみ</h3>
    <p>小さくて安価なコンピュータ”RaspberryPi”で『場』のwi-fiネットワークに繋がったスマホやパソコンの固有ID（MACアドレス）を調べて、持ち主である『誰』が居るかをウェブシステムに送信し、PCやスマホのチャットアプリにお知らせします。</p>
    <figure>
        <img src="{{asset("img/home/RaspberrypiIMGL3473_TP_V.png")}}" width="50%" alt="slack sample画像">
        <figcaption>※専用端末 RaspberryPi 3 安価な小型パソコンで消費電力も約2.5W/hと極めて低コスト。初期設定後は電源ケーブルのみで24時間稼働します。</figcaption>
    </figure>

    <figure class="pc">
        <img src="{{asset("img/home/screenshot_info.png")}}" width="30%" alt="slack sample画像">
        <figcaption>※スマホにSlack等でリアルタイムな通知が受け取れます</figcaption>
    </figure>
    <figure class="sp">
        <img src="{{asset("img/home/screenshot_info.png")}}" style="max-width: 60%;" alt="slack sample画像">
        <figcaption>※スマホにSlack等でリアルタイムな通知が受け取れます</figcaption>
    </figure>

    <h3>PC・スマホの固有番号(MACアドレス)は暗号化して送信・管理</h3>
    <figure>
        <img src="{{asset("img/home/encryption_summary.png")}}" class="pc" width="80%" alt="slack sample画像" style="border: 0px solid #fff;">

        <img src="{{asset("img/home/encryption_summary_sp1.png")}}" class="sp" width="100%" alt="slack sample画像" style="border: 0px solid #fff;">
        <img src="{{asset("img/home/encryption_summary_sp2.png")}}" class="sp" width="100%" alt="slack sample画像" style="border: 0px solid #fff;">
    </figure>


    <h3>チャットアプリはネットコミュニティとしても活用可能</h3>

    <p>『場』のメンバーだけが知っている非公開ウェブサイトで『今誰がいる』かを閲覧したり、SlackやLINE等のチャットアプリと連携して、来訪者や帰宅者の通知をスマホやパソコンでリアルタイムに知る事が出来ます。</p>
    <figure class="pc">
        <img src="{{asset("img/home/site_image_1.png")}}" width="30%" alt="slack sample画像">
        <figcaption>※会員だけが知るウェブサイトでは現在の滞在者がわかります</figcaption>
    </figure>
    <figure class="sp">
        <img src="{{asset("img/home/site_image_1.png")}}" style="max-width: 50%;" alt="slack sample画像">
        <figcaption>※会員だけが知るウェブサイトでは現在の滞在者が判ります</figcaption>
    </figure>

    <figure>
        <img src="{{asset("img/home/sample_slack.png")}}" width="60%" alt="slack sample画像">
        <figcaption>※Slack等はコミュニティ専用のチャットとしても利用できます、すでにSlackをご利用されている場合は、そこに通知を行うことも可能です。</figcaption>
    </figure>

    <h2>プライバシーにも配慮</h2>
    <p>専用サイトやチャットルームでコミュニティのメンバーだけが、その『場』の人の存在を伺い知る事ができます。ウェブサイトのユーザーは最初は仮名で登録されますので、自分やコミュニティのオーナーが登録しない限り、誰が来たかはわからない様になっています。それでもこっそり訪れたいユーザーは非表示設定が可能です。(ログインが必要です)</p>


    <h3>実際のシェアオフィスのニーズから生まれました</h3>
    <p>会員制シェアオフィス兼イベントスペースの『ギークオフィス恵比寿』での運営者のノウハウとニーズをシステム化した仕組みです。突発的に人が集まったり、思わぬ出会いが促進されイノベーションが起こった結果、このサービスが生まれました。導入後は、より多くの人が集い新しいものが生まれ続けています</p>

    <h3>作ったのは？</h3>
    <p>ギークオフィス恵比寿の会員で40歳を過ぎてからプログラミングを始めたIT講師です。</p>
    <p>シェアオフィスで「今誰が居るかを知りたい」という要望を元に、基本部分を一人で一月かけて開発しました。（企画やデザインは共同作業です）</p>
    <p>既にギークオフィス恵比寿では多くのメンバーに使ってもらっています。また現在は多くのコミュニティで使って頂けるよう改修作業に勤しんでします。</p>
    <p><a href="https://sakamata.hateblo.jp/entry/livelynk_develop_full_account" target="_brank">開発経緯等をまとめたブログはこちら</a></p>

    <h3>価格は？</h3>
    <p>本体RasberryPi 及びソフト一式で20,000円～（税別）<br>
        また月額利用費 1,000円～（税別 端末登録数に応じて変更）<br>
        ですがもし、この仕組みにより価値を感じて頂いたら、より多くの価格で購入して欲しいと思っています。</p>

    <h3>お問い合わせ</h3>
    <p>現在は以下のメールアドレスより受け付けています。<br>
    <a href="mailto:livelynk.jp@gmail.com?subject=Livelynk購入のお問い合わせ">livelynk.jp@gmail.com</a>
    </p>

    <h3>ご購入</h3>
    <P>現在、2019年2月末日まで、都内近郊でテスト導入していただけるコミュニティを若干数募集しています。（テスト期間中は月額費用はいただきません。）</P>
    <p>端末のお届けとご利用開始は、2018年12月以降より順次発送を予定してします。</p>
    <p>応募が定員に達し次第ご購入を締め切らせていただきます。</p>

    <div class="pc" style="text-align: center;">
        <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdjITmdnBXZCcMsb_3qFenz-khesv_EgpmxerBoIaYwKEzaRQ/viewform?embedded=true" width="800" height="1583" frameborder="0" marginheight="0" marginwidth="0">読み込んでいます...</iframe>
    </div>

    <div class="sp" style="text-align: center;">
        <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdjITmdnBXZCcMsb_3qFenz-khesv_EgpmxerBoIaYwKEzaRQ/viewform?embedded=true" width="300" height="2100" frameborder="0" marginheight="0" marginwidth="0">読み込んでいます...</iframe>
    </div>

    {{-- 
    <h3>『場』の持つ力</h3>
    <p>シェアオフィスやコワーキングスペース、趣味のコミュニティ等、シェアリングエコノミーやコミュニティが価値を持つ時代になりました。昔と比べて、好きな時間に好きな場所で好きな人同士で好きな事をできる時代になりつつある事を実感する人は多いのではないでしょうか？</p>
    <p>『場』が盛り上がる事で単に楽しいだけでなく、新しい繋がりや、関係、モノが生まれる可能性を秘めた時代に来ていると思いませんか？</p>

    <h3>『場』が盛り上がらないのは何故？</h3>
    <p>でも、あなたか集うそのリアルな『場』っていつも盛り上がってますか？
    実は『場』で今一つ足りないものは、求心力だったりしませんか？任意の人が任意の時間に集うユルいコミュニティでは、どうしても時間的なすれ違いが発生しがちです。かといって、毎回時間を決めて集まるというのも少し違う気がします。また、コミュニティの中心人物がとても頑張って場に集まる機会を作り盛り上げていかなければいけません。しかし、もっと自由で、気楽に、『場』の持つ求心力を上げる方法はないのでしょうか？</p>

    <h3>『誰か』をきっかけに集い『何か』が生まれる世界を</h3>
    <p>そんなニーズに答える仕組みがとあるシェアオフィスで生まれました。5年以上のコミュニティのノウハウを集約させ、人々が気楽に集い、新しい何かを生み出す仕組みを企画し、製作しました。それがLivelynk（ライブリンク）です。</p>
    <p>Livelynkとは　Lively（活発な・賑やかな）  と Link（関連・きずな）  を掛け合わせた造語です。</p> --}}


</div>
@endsection