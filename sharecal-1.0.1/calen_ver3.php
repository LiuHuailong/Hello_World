<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-script-type" content="text/javascript">
<title>シェアカレ MyCalendar</title>
<link href="share_ver2.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap.min.css" rel="stylesheet">
<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
<script src="bootjs/bootstrap.min.js"></script>
<script type="text/javascript">

// 祝日1：何月の何日か？
        var Holidays1 = new Array(1,1, 2,11, 3,21, 4,29, 5,3, 5,4, 5,5, 8,11, 9,22, 11,3, 11,23, 12,23);
        // 祝日2：何月の第何月曜日か？
        var Holidays2 = new Array(1,2, 7,3, 9,3, 10,2);
        // グループ入力フラグ
        var Gflag = 0;
        //グループ予定の変更が可能かのフラグ
        var Editflag = 0;
        //グループモード判定用フラグ
        var Gmode = 0;
        // 現在の年、月、日の取得
        var now       = new Date();
        var thisYear  = now.getFullYear();
        var thisMonth = now.getMonth() + 1;
        var today     = now.getDate();

        //入力時間
        var starth = "00";
        var startm = "00";

        // 表示年月の記憶
        var year      = thisYear;
        var month     = thisMonth;

        var monthdays = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        var days = new Array("日", "月", "火", "水", "木", "金", "土");

       //入力情報の格納変数
        var todotitle;
        var starttime;
        var todomsg;

       //日付データが入っている変数
       var yy;
       var mm;
       var day;

      //ユーザー名
        var username = window.sessionStorage.getItem(['user']);
      //ユーザーID
        var userid;

      //一番最初かどうかのチェック
        var countcheck = 0;

        function showCalen(n){
        month += n;

        if (month == 0) { year--; month=12; }
        else if (month == 13) { year++; month=1; }
        var flag = ((year == thisYear) && (month == thisMonth))? 1: 0;

        var date = new Date(year, month-1, 1);
        var startDay = date.getDay();

        var dateMax = monthdays[month - 1];
        if (month == 2 && ((year%4 == 0 && year%100 != 0) || year%400 == 0)) dateMax = 29;

        // 休日配列の初期化
        var holidays = new Array();
        for (var i=0; i<=dateMax; i++) holidays[i] = 0;

        // 祝日1 の処理
        var firstSunday = (startDay == 0)? 1: 8 - startDay;
        for (i=0; i<Holidays1.length; i+=2) {
        if (Holidays1[i] == month) {
        holidays[Holidays1[i+1]] = 1;
        for (var j=firstSunday; j<dateMax; j+=7)
        if (Holidays1[i+1] == j) holidays[j+1] = 1;  // 振替休日
        }
        }
        // 祝日2 の処理
        var mondays = new Array();
        var firstMonday = (startDay < 2)? 2 - startDay: 9 - startDay;
        for (i=0; i<Holidays2.length; i+=2)
        if (Holidays2[i] == month) holidays[(Holidays2[i+1] - 1) * 7 + firstMonday] = 1;

        var htmlStr = "<table class='calen'>\n"
        + "<tr class='bg1'><th colspan=7>" +"<input type='button' value='<<' id='back' onclick='restart(-1)'>" + year + "年 " + month + "月" + "<span class='dayday'>" + today + "</span>" + "日" + "<input type='button' value='>>' id='next' onclick='restart(1)'>" + "</th></tr>\n";
        htmlStr +="<tr class='bg2'><th class='sun'>" + days[0] + "</th>";
        for (i=1; i<6; i++) htmlStr += "<th>" + days[i] + "</th>";
        htmlStr += "<th class='sat'>" + days[6] + "</th></tr>\n";

        var col=0;
        if (startDay > 0) {
        htmlStr += "<tr class='daydate'>";
        for ( ; col<startDay; col++) htmlStr += "<td>&nbsp;</td>";
        }
        for (i=1; i<=dateMax; i++) {
        if (col == 0) htmlStr += "<tr class='daydate'>";
        if (flag == 1 && i == today) {
        if (holidays[i] == 1 || col == 0) htmlStr += "<td class='today sun' yy='"+year+"' mm='"+month+"' day='"+i+"' onclick='test(this);return false;'>";
        else if (col == 6) htmlStr += "<td class='today sat' yy='"+year+"' mm='"+month+"' day='"+i+"' onclick='test(this);return false;'>";
        else htmlStr += "<td class='today' yy='"+year+"' mm='"+month+"' day='"+i+"' onclick='test(this);return false;'>";
        }
        else if (holidays[i] == 1 || col == 0) htmlStr += "<td class='sund' yy='"+year+"' mm='"+month+"' day='"+i+"' onclick='test(this);return false;'>";
        else if (col == 6) htmlStr += "<td class='satd' yy='"+year+"' mm='"+month+"' day='"+i+"' onclick='test(this);return false;'>";
        else htmlStr += "<td class='normal' yy='"+year+"' mm='"+month+"' day='"+i+"' onclick='test(this);return false;'>";
        if (i == 25){
            htmlStr += i +"<h6>" + "●" + "</h6>" ;
        }
        else{
            htmlStr += i +"<h6>" + "&nbsp;" + "</h6>";
        }
        htmlStr += "</td>";
        if (col == 6) { htmlStr += "</tr>\n"; col=0; } else col++;
        }
        if (col != 0) {
        for ( ; col<7; col++) htmlStr += "<td>&nbsp;</td>";
        htmlStr += "</tr>";
        }
        htmlStr += "</table>";
         document.getElementById("Group_calen").innerHTML = htmlStr;
         $('.calendate').text(year + '/' + month + '/' + today);
        if(Gflag != 1){
           document.getElementById("calen").innerHTML = htmlStr;
        }
        else{
         document.getElementById("Group_calen").innerHTML = htmlStr;
        }

        if(countcheck == 0){
        username = window.sessionStorage.getItem(['user']);
        $('#menu_ber h1').text(username+"様のカレンダー");
        countcheck++;
        // for(){
           $('.Grouptext').append($('<div></div>').append($('<div class="Grouplist"></div>').text('jijijijiji'))
        .append('<button class="btn btn-danger btn-xs delete_Group">削除</button>'));
        // }
        }
      }

        //日付クリック時の処理
        function test(e){
        var openset = '<div id="tw1" onclick="showopen()">' + '<div class="tw">' + "＋" + '<div>' + '</div>';
            //日付情報の取得
            yy = e.getAttribute('yy');
            mm = e.getAttribute('mm');
            day = e.getAttribute('day');
            $('.calendate').text( yy + '/' + mm + '/' + day);
            //入力画面の表示、非表示の関数呼び出しのクリックイベント

        /*-----データベースの処理---------------*/
            // todo:予定があれば表示
            //Gmodeが１の場合グループカレンダーの予定表示

        if(Gflag != 1){
            //表示していた予定の削除
            $('#calen .dayday').text(day);
            $('#todotable' ).find("tr:gt(0)").remove();         //全行削除
            $('.todolist').css('display' , 'none');
            $('#tw2').css('display' , 'block');
            document.getElementById("tw2").innerHTML = openset;
        //     if(Gmode == 1 && Editflag != 1){            //グループ予定が変更できない人の処理
        //       $('#tw2').css('display' , 'none');
        //       $('.delete').css('visibility' , 'hidden');
        //     }
        }
        else{
              //表示していた予定の削除
             $('#Group_calen .dayday').text(day);
            $('#todotable2' ).find("tr:gt(0)").remove();         //全行削除
            $('.todolist2').css('display' , 'none');
             $('#tw2').css('display' , 'block');
            document.getElementById("tw3").innerHTML = openset;
        }
        }

        // 時間取得
         function outputSelectedStarthour(obj){
             var idx = obj.selectedIndex;
                starth= obj.options[idx].text;  // 表示テキスト
         }
         function outputSelectedStartmin(obj){
             var idx = obj.selectedIndex;
                startm= obj.options[idx].text;  // 表示テキスト
         }

        function settodo(title,message){
                       //入力したデータを変数に代入
                if(title.length > 15){
                    alert("15文字以内で入力してください");
                    return;
                }
            todotitle = title;
            starttime = starth + ":" + startm;
            todomsg = message;
            // データベースの処理で入力情報を格納

            if(Gflag != 1){ //プライベート予定追加
            $('.todolist').css('display' ,'block')
            $('tbody' , '.todolist').append($('<tr class="active"></tr>')
            .append($('<td class="tit" onclick="showtodo(this)"></td>').text(title))
            .append($('<td class="ac1"></td>').append('<button type="button" class="btn btn-danger delete"> Delete</button>')));
            }
            else{   //グループ予定追加
             $('#open2').css('display' , 'none');
            $('.todolist2').css('display' ,'block')
            $('tbody' , '.todolist2').append($('<tr class="active"></tr>')
            .append($('<td class="tit" onclick="showtodo(this)"></td>').text(title))
            .append($('<td class="ac1"></td>').append('<button type="button" class="btn btn-danger delete"> Delete</button>')));
            }
              $('.textinputs').removeClass('on');
              $('.textinputs').animate({'marginLeft':'-400px'},300).addClass('off');
              $('.tw').text("＋");
            $('.settitle').val('');
            $('select') .prop('selectedIndex', 0);
            $('.msg').val('');
            //スマホ用処理
          if($('.smartinput').hasClass('on')){
          $('.smartinput').removeClass('on');
          $('.smartinput').animate({'height':'0vh'},300).addClass('off');
          if(Gmode == 1){
                    $('.release').css('visibility' , 'visible');
          }
          $('.main').css('display' , 'block');
          if(Gflag != 1){
          $('.Groupsection').css('display' , 'block');
      }
          $('.smartadd').text('+');
        }

      }

        function restart(n){
        $('#todotable' ).find("tr:gt(0)").remove();         //全行削除
        $('.todolist').css('display' , 'none');
        $('#tw2').text("");
        // $('#open').css('display' , 'none')
        showCalen(n);
        }
        function showopen(){
        $(function(){
        if(Gflag != 1){                                   //入力画面の表示、非表示
          if($('.Ptextinputs').hasClass('off')){
             $('.Ptextinputs').removeClass('off');
             $('.Ptextinputs').animate({'marginLeft':'100px'},300).addClass('on');
            $('.tw').text("閉じる");
            }
            else{
                $('.Ptextinputs').removeClass('on');
                 $('.Ptextinputs').animate({'marginLeft':'-400px'},300).addClass('off');
                $('.tw').text("追加");
            }
        }
        else{ if($('.Gtextinputs').hasClass('off')){
             $('.Gtextinputs').removeClass('off');
             $('.Gtextinputs').animate({'marginLeft':'100px'},300).addClass('on');
            $('.tw').text("ー");
            }
            else{
                $('.Gtextinputs').removeClass('on');
                 $('.Gtextinputs').animate({'marginLeft':'-400px'},300).addClass('off');
                $('.tw').text("＋");
            }}
         } );
         }
        //入力した内容を表示
        function showtodo(obj) {
          var rowNum = obj.parentNode.rowIndex;
         alert(rowNum);
        var showtodotext =  '<div class="textinputs">' + '<div>' + '<span class= "showtext1">' + "タイトル"  + '</span>' + '<div class ="text1">' + todotitle + '</div>' + '</div>' ;          //タイトル
        showtodotext += '<div>' + '<span class ="showtext1">' + "時間" + '</span>' + '<div class="todotime">' + starttime + "~"  + '</div>' + '</div>';        //時間
        showtodotext += '<div>' + '<span class ="showtext1">' + "メッセージ" + '</span>' + '<div class="text2">' + todomsg + '</div>' + '</div>';           //メッセージ
        showtodotext += '<div class="button">' + '<input type="button" value="閉じる" onclick="closetodo()">' + '</div>';
        showtodotext += '</div>';
        if(Gflag != 1){
        $('.Ptodo').html(showtodotext);

         $('.Ptodo').removeClass('off');
         $('.Ptodo').animate({'marginLeft':'500px'},300).addClass('on');
        }
        else{
             $('.Gtodo').html(showtodotext);

         $('.Gtodo').removeClass('off');
         $('.Gtodo').animate({'marginLeft':'500px'},300).addClass('on');
        }

         //スマホ用の処理
         $('.text1').text(todotitle);
         $('.todotime').text(starttime + "~" );
         $('.text2').text(todomsg);
         if($('#smartmenu').css('display') == 'block'){
         $('.todolist').css('display' , 'none');
         if(Gflag == 1){
          $('.todolist2').css('display' , 'none');
          $('#createin').css('display' , 'none');
          $('#Group_create_button').css('display' , 'none');
         }
         $('.smartout').animate({'height': '100vh'});
         $('.smartadd').css('visibility' , 'hidden');
         $('.smartdel').css('visibility', 'visible');
         $('.release').css('visibility' , 'hidden');
         $('.main').css('display' , 'none');
         $('.Groupsection').css('display' , 'none');
       }
}


    function closetodo(){
        $('.todo').text(" ");
        $('.todo').removeClass('on');
        $('.todo').animate({'marginLeft':'1000px'},300).addClass('off');

        //スマホ用処理
        if($('#smartmenu').css('display') == 'block'){
        $('.smartout').animate({'height': '0vh'});
        $('.smartdel').css('visibility' , 'hidden');
        $('.smartadd').css('visibility' , 'visible');
        if(Gmode == 1){
            $('.release').css('visibility' , 'visible');
        }
         $('.main').css('display' , 'block');
         if(Gflag==0){
         $('.Groupsection').css('display' , 'block');
         $('.todolist').css('display' , 'block');
          }
          else{
          $('.todolist2').css('display' , 'block');
          $('#createin').css('display' , 'block');
          $('#Group_create_button').css('display' , 'block');
          }
       }
    }

    $(function(){
         for(var i = 0 ; i < 24; i++){  //プルダウン
            $('.selectedate-h').append('<option value="' + i + '">' + ("0" + i).slice(-2) + '</option>');
        }

        for(var i = 0 ; i < 60; i++){
            $('.selectedate-m').append('<option value="' + i + '">' + ("0" + i).slice(-2) + '</option>');
        }

        $('.smartadd').on('click', function(){
              if($('.smartinput').hasClass('off')){
            $('.smartinput').removeClass('off');
            $('.smartinput').animate({'height': '100vh' },300).addClass('on');
            $('.main').css('display' , 'none');
            $('.todolist').css('display' , 'none');
            $('.Groupsection').css('display' , 'none');
            $('.release').css('visibility' , 'hidden');
            $('.smartadd').text('ー');
          }else{
            $('.smartinput').addClass('off');
            $('.main').css('display' , 'block');
            if(Gflag != 1){
            $('.Groupsection').css('display' , 'block');
             }
            $('.smartinput').animate({'height':'0vh'},300);
            $('.smartadd').text('＋');
            if($('#todotable .delete').length != 0){
                $('.todolist').css('display' , 'block');
            }
            if(Gmode == 1){
                $('.release').css('visibility' , 'visible');
            }
          }
        });
        //予定の削除ボタンクリック時
        $(document).on('click' , '.delete', function(){
            if(Gflag != 1){
                 if(!confirm('本当に削除しますか？')){
                    return false;
                }
                else{
                       var rowNum = $('#todotable .delete').index(this);
                       $('#todotable').find('tr').eq(rowNum + 1).remove();
                   if($('#todotable .delete').length == 0){
                     $('.todolist').css('display' , 'none');
                          //プライベートカレンダーの予定削除
                     }
                }
            }
            else{
                var rowNum = $('#todotable2 .delete').index(this);
            $('#todotable2').find('tr').eq(rowNum + 1).remove();
            if($('#todotable2 .delete').length == 0){
                $('.todolist2').css('display' , 'none');
                //グループカレンダーの予定削除
            }
            }
            // todo:関連する情報をデータベースから削除する
            }

        );

        $(document).on('click','.delete_Group', function(){     //グループの削除ボタンクリック時
            // alert($(this).prev('div').text());
            if(!confirm('本当に削除しますか？')){
                return false;
            }
            else{
                if($(this).prev('div').text() == $('#menu_ber h1').text()){
                //グループモードの解除
                $('#menu_ber h1').text(username+"様のカレンダー");
                $('.release').css('visibility','hidden');
                $('.logboutton').text("Logout");
                $('#GroupEdit').css('visibility' , 'visible');
                Gmode = 0;
                $('html,body').animate({scrollTop:0}, "slow");
            }
            $(this).parent().remove();
        }
            // todo:関連する情報をデータベースから削除する


        });

    $('.edit_Group').on('click',function(){        //グループID入力　スライド
  if($('.slide').hasClass('off')){
    $('.slide').removeClass('off');
    $('.slide').animate({'marginLeft':'0px'},300).addClass('on');
    $('#titleh h3').text('グループID入力');
  }else{
    $('.slide').addClass('off');
    $('.slide').animate({'marginLeft':'-300px'},300);
    $('#titleh h3').text('登録したグループ名')
  }
});
    $('.create_Group').on('click',function(){       //グループカレンダー入力
        $('#createin').css('display' , 'block');
        $('.logboutton').css('visibility','hidden');
        $('#menu_ber h1').css('display' , 'none');
        $('#Group_title').css('display','block');
        $('.Groupsection').css('display','none');
        if($('#smartmenu').css('display') == ('block')){
          $('.smartadd').css('visibility' , 'hidden');
        }
        Gflag = 1;
    });

    $('.cancel_Group').on('click',function(){　 // グループカレンダー入力解除(cancel)
        $('#Group_calen').css('display' , 'none');
        $('#createin').css('display', 'none');
        $('.logboutton').css('visibility', 'visible');
        $('#Group_title').css('display','none');
        $('#menu_ber h1').css('display', 'block').text(username + "様のカレンダー")
        $('.Groupsection').css('display','block');
        $('#todotable2' ).find("tr:gt(0)").remove();         //予定全行削除
        $('.todolist2').css('display' , 'none');
        $('#Group_title').val('');
        $('#tw3').css('display' , 'none');
         if($('#todotable .delete').length != 0){ //プライベートで予定があった場合の処理
                $('.todolist').css('display' , 'block');
          }
         if($('#smartmenu').css('display') == ('block')){ //タイトル入力せずにキャンセルボタンを押した場合の備え
          $('.smartadd').css('visibility' , 'visible');
        }
        Gflag = 0;

    });

     $('.ok_Group').on('click',function(){      //OKボタンクリック時
        if($('#Group_title').val().length > 15){
            alert("15文字以内で入力してください");
            return;
        }
        else if($('#Group_title').val().length == 0){
            alert('タイトルを入力してください');
            return;
        }
        if($('#Group_calen').css('display') == ('none') && $('#Group_title').val().length > 0){
           $('#Group_title').css('display' , 'none');
           $('#menu_ber h1').css('display' , 'block').text($('#Group_title').val());
           $('#Group_calen').css('display' , 'block')
        if($('#smartmenu').css('display') == ('block')){ 
          $('.smartadd').css('visibility' , 'visible');
        }
           return;
        }
        // alert($('#Group_title').val().length);
        var gtitle = $('#Group_title').val();
        // alert(gtitle);
        $('#todotable2' ).find("tr:gt(0)").remove();         //予定全行削除
        $('.todolist2').css('display' , 'none');
        $('#Group_title').val('');
        $('#tw3').css('display' , 'none');
        $('#Group_calen').css('display' , 'none');
        $('#createin').css('display', 'none');
        $('.logboutton').css('visibility' , 'visible');
        $('#menu_ber h1').text(username + "様のカレンダー");
        $('.Groupsection').css('display' , 'block');
        if($('#todotable .delete').length != 0){
                $('.todolist').css('display' , 'block');
          }
        Gflag = 0;
        $('.Grouptext').append($('<div></div>').append($('<div class="Grouplist"></div>').text(gtitle))
        .append('<button class="btn btn-danger btn-xs delete_Group">削除</button>'));
    });
    $(document).on('click','.Grouplist', function(){  //グループリストクリック時
        var gtext = $(this).html();
        // alert(gtext);
        $('#todotable' ).find("tr:gt(0)").remove();         //全行削除
        $('.todolist').css('display' , 'none');
        $('#menu_ber h1').text(gtext);
        $('html,body').animate({scrollTop:0}, "slow");
        // return false;
        $('.release').css('visibility' , 'visible');
        if($('#smartmenu').css('display')==('none')){
        $('.logboutton').text("mycalendarに戻る")
        }
        else{
          $('.logboutton').css('visibility' , 'hidden');
        }
        $('#GroupEdit').css('visibility' , 'hidden');

        Gmode = 1;
        //gtextのタイトルのデータベースを検索し、カレンダーを再読み込み
        //
        //

        showCalen(0);

    });
    $('.release').on('click', function(){       //スマホ用
        //グループモードの解除(データベースをプライベートに戻す)
         $('#menu_ber h1').text(username+"様のカレンダー");
         $('.release').css('visibility', 'hidden');
         $('#GroupEdit').css('visibility' , 'visible');
         $('.logboutton').css('visibility' , 'visible');
         Gmode = 0;
         showCalen(0)
    });

    $('.logboutton').on('click',function(){     //PC用の解除とログアウト
        if(Gmode == 0){             //ログアウト処理
            if (confirm("ログアウトします。よろしいですか?")){
                localStorage.clear();
                window.location.href = 'index.html';
            }

        }
        $('#menu_ber h1').text(username+"様のカレンダー");
        $('.logboutton').text("Logout");
        $('#GroupEdit').css('visibility' , 'visible');
        Gmode = 0;
        showCalen(0);
    })

     $(document).on({   //追加ボタンのホバーエフェクト  グループモード後で実装
       "mouseenter": function(){
        $(this).animate({'width' : '100px'}, 100);
         if($('.Ptextinputs').hasClass('off')){
            $(this).text('追加');
         }
         else{
            $(this).text('閉じる');
         }

       },
       "mouseleave": function(){
        $(this).animate({'width' : '40px'}, 100);
         if($('.Ptextinputs').hasClass('off')){
             $(this).text('＋');
          }
          else{
            $(this).text('ー');
          }

   }
     } , '.tw');
     $('.join').on('click' , function(){
        //データベース側の処理
         var i = jointext.value;
         // alert(i);
         if(i == "ji"){
         $('.Grouptext').append($('<div></div>').append($('<div class="Grouplist"></div>').text("test"))
        .append('<button class="btn btn-danger btn-xs delete_Group">削除</button>'));
         $('.slide').addClass('off');
         $('.slide').animate({'marginLeft':'-300px'},300);
         $('#titleh h3').text('登録したグループ名')
         }
         else{
         alert("グループがありません");
     }
     })
    });



</script>


        </head>
        <body onload="showCalen(0)">
        <div id="menu_ber">
            <h1>ゲスト様のカレンダー</h1>
            <input type="text" id="Group_title" placeholder="グループ名の入力(15文字以内)">
                 <button class="btn btn-default logboutton">Logout</button>
        </div>
            <div id ="smartmenu">
                <button class="release">MyCalendarに戻る</button>
                <button class="smartadd">＋</button>
                <button class="smartdel" onclick="closetodo();">x</button>
            </div>
            <div class="smartinput off">
              <div class="calendate"></div>
              <div>
              <label>タイトル</label><br>
              <input type="text" class="settitle" id="ssettitle"  placeholder="15文字以内で入力してください">
            </div>
            <div>
              <label>時間</label><br>
              <select name="selectedate-h" class="selectedate-h" onChange="outputSelectedStarthour(this);">
              </select>:<select name="selectedate-m" class="selectedate-m" onChange="outputSelectedStartmin(this);">

              </select>~
            </div>
            <div>
              <label>メッセージ</label><br>
              <textarea class="msg" id="smsg"></textarea>
            </div>
            <div class="sbutton"><input type="button" value="追加" onclick="settodo(ssettitle.value,smsg.value);"></div>
            </div>
            <div class="smartout">
                <div>
                <label>タイトル</label><br>
                    <div class="text1"></div>
                </div>
                <label>時間</label><br>
                    <div class="todotime">
                </div>
                <div>
                <label>メッセージ</label><br>
                    <div class="text2 stext"></div>
                </div>
            </div>

       <div class="main">
         <div id="open">
        <p class="inputmenu">
            <div class="textinputs Ptextinputs off">
            <div>
                <!-- 日付データ -->
             <div class="calendate"> </div>
                <!-- タイトル -->
            <div><label for="settitle">タイトル</label>
            <input type="text" class="settitle" id="settitle" placeholder="15文字以内で入力してください"></div>
                    <!-- 時間 -->
                <div><label for="categ"> 時間</label><select name="selectedate-h" class="selectedate-h" onChange="outputSelectedStarthour(this);">

</select>:<select name="selectedate-m" class="selectedate-m" onChange="outputSelectedStartmin(this);">

</select>~</div>
                     <!-- メッセージ -->
                    <div><label for="msg">メッセージ</label><textarea class="msg" id="msg"></textarea></div>
                        <!-- ボタン -->
                        <div class="button"><input type="button" value="追加" onclick="settodo(settitle.value,msg.value);"></div>
            </div>
        </div>
      </p>
    </div>
        <p id="calen"></p>
        <div class="todo Ptodo off"></div>
    </div>

        <div class="todolist" style="display: none">
        <table class="table table-bordered table-hover table-condensed" id="todotable">
         <thead class="scrollHead">
             <tr>
                <th class="tit">予定</th>
                <th class="ac1"> </th>
                <!-- <th class="ac2"> </th> -->
             </tr>
         </thead>
         <tbody class="scrollBody">
         </tbody>
         <tfoot>
         </tfoot>
        </table>
        </div>

             <div class="Groupsection">
            <div class="GroupBox">
            <div id="titleh">
            <h3>登録したグループ名</h3>
            </div>
            <div class="slide off">
                <input type="text" id="jointext"><button type="button" class="btn btn-success join">参加</button>
            </div>
            <div class="Grouptext">
    <!--         <div>
                <div class="Grouplist">神戸電子専門学校　行事</div><button class="btn btn-danger btn-xs delete_Group">削除</button>

            </div>
            <div>
                <div class="Grouplist">アルバイトシフト</div><button class="btn btn-danger btn-xs delete_Group">削除</button>
            </div>
            <div>
                <div class="Grouplist">就活関連の予定</div><button class="btn btn-danger btn-xs delete_Group">削除</button>
            </div>
            <div>
                <div class="Grouplist">グループ開発　スケジュール</div><button class="btn btn-danger btn-xs delete_Group">削除</button>
            </div> -->
        </div>
        </div>
           <p id="GroupEdit"><button class="btn btn-primary edit_Group">新しいグループに参加</button>
         <button class="btn btn-primary create_Group">グループを作る</button>
        </p>
        </div>

        <!-- 折りこみ展開ポインタ -->
        <div id="tw2"> </div>

        <!-- 折り畳まれ部分 -->

        <div id="createin">
            <div class="main">
            <p id="inputmenu2">
          <div class="textinputs Gtextinputs off">
            <div>
                <!-- 日付データ -->
             <div class="calendate"> </div>
                <!-- タイトル -->
             <label for="settitle">タイトル</label><input type="text" class="settitle" id="settitle2" placeholder="15文字以内で入力してください"></div>
                    <!-- 時間 -->
                <div><label for="categ"> 時間</label><select name="selectedate-h" class="selectedate-h" onChange="outputSelectedStarthour(this);">

</select>:<select name="selectedate-m" class="selectedate-m" onChange="outputSelectedStartmin(this);">

</select>~</div>
                     <!-- メッセージ -->
                    <div><label for="msg">メッセージ</label><textarea class="msg" id="msg2"></textarea></div>
                        <!-- ボタン -->
                        <div class="button"><input type="button" value="追加" onclick="settodo(settitle2.value,msg2.value);"></div>
            </div>
            </p>
            <div id="Group_calen"></div>
            <div class="todo Gtodo off"></div>
        </div>
         <div class="todolist2" style="display: none">
        <table class="table table-bordered table-hover table-condensed" id="todotable2">
         <thead class="scrollHead">
             <tr>
                <th class="tit">予定</th>
                <th class="ac1"> </th>
                <!-- <th class="ac2"> </th> -->
             </tr>
         </thead>
         <tbody class="scrollBody">
         </tbody>
         <tfoot>
         </tfoot>
        </table>
        </div>
         <div id="tw3"> </div>

        <!-- 折り畳まれ部分 -->

        <p id="Group_create_button"><button class="btn btn-danger ok_Group">完了</button>
         <button class="btn btn-success cancel_Group">キャンセル</button>
        </p>
        </div>
        </body>
        </html>
