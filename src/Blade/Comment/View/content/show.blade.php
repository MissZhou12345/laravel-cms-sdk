<div class="layout_comment comment" id="comment_{{$commentKey}}_{{$linkIdStr}}">

    @if($needLogin)
    <p class="comment">发表评论</p>
    <div class="form">
        <form name="comment_create" id="comment_form_{{$commentKey}}_{{$linkIdStr}}" data-container="comment_list_{{$commentKey}}_{{$linkIdStr}}" action="{{$_addUrl}}"
              class="form-horizontal form-validation comment_replay" accept-charset="UTF-8" method="post">
        <div class="textarea">
            <textarea @if(!$_user) onClick="Log.logShow();" @endif id="comment_text_{{$commentKey}}_{{$linkIdStr}}" PLACEHOLDER="说说你的看法" name="content"></textarea>
        </div>
        <div class="publish clear">
            <button class="icon_button3 submit" type="button">发表</button>
            <span>可以输入<i id="comment_text_limt_{{$commentKey}}_{{$linkIdStr}}">{{ $limit }}</i>字</span>
        </div>
            <input type="hidden" name="comment_key" value="{{$commentKey}}"/>
            <input type="hidden" name="source_title" value="{{$sourceTitle}}"/>
            <input type="hidden" name="link_id" value="{{$linkIdStr}}"/>
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
        </form>
    </div>
    @else
    <p class="comment">用户评论</p>
    @endif



    <div  id="comment_list_{{$commentKey}}_{{$linkIdStr}}">
        @include('Comment::content.list')
    </div>


    <script type="text/javascript">
        function ajaxfn2(){
            var _textMax = function(){

                $api.textMax({
                    len : "{{ $limit }}",//限制输入的字符个数
                    showId : "#comment_text_limt_{{$commentKey}}_{{$linkIdStr}}",//显示剩余字符文本标签的
                    textThis:"#comment_text_{{$commentKey}}_{{$linkIdStr}}"
                });
            };
            _textMax();

            $("#comment_form_{{$commentKey}}_{{$linkIdStr}} .submit").click(function() {

                var from = $("#comment_form_{{$commentKey}}_{{$linkIdStr}}");
                var container =  $("#comment_list_{{$commentKey}}_{{$linkIdStr}}");
                if(from.find("textarea").val()=="") {
                    layer.msg('请输入内容');
                    return false;
                }

                layer.load();
                $api.ajax({
                    type: "POST",
                    url:from.attr("action"),
                    data:from.serialize(),
                    dataType:"json"},
                    function(data){
                        if(data.status==200) {
                            from.find("textarea").val("");
                            container.prepend('<div class="comment_Lit">\
                                                <div class="ency_text">\
                                                <p class="user"><span class="tel">'  + data.data.operator_nickname + ' </span><span class="timer">刚刚</span></p>\
                                                <pre style="white-space: pre-wrap; padding:0 30px 0 30px; position: relative; top: -3px;">' + data.data.content + '</pre>\
                                                </div>\
                                                </div>');

                            _textMax();
                            layer.msg('评论成功');
                        }else{
                            layer.msg(data.msg);
                        }
                        layer.closeAll('loading');
                });
                return false;
            });



        };
    if(typeof $ == "undefined"){
            setTimeout(function(){
                ajaxfn2();
            },1000);
        }else{
           ajaxfn2();
        }

    </script>
</div>