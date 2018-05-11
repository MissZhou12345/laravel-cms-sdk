@if(isset($list))

    @foreach($list->data as $item)
        <div class="comment_Lit">
            <div class="ency_text">
                <p class="user"><span class="tel">{{$item->operator_nickname or ''}} </span><span
                            class="timer">{{showWriteTime(strtotime($item->created_at))}} </span></p>
                {{--<p class="ans">{{$item->content}}</p>--}}
                <pre style="white-space: pre-wrap; padding:0 10px 0 30px; position: relative; top: -3px;">{!! $item->content !!}</pre>

                @if($needLogin)
                    <p class="reply_btn"><a href="javascript:;">回复</a></p>
                @endif

                <div class="comment_textarea" style="display: none;">
                    <form name="comment_replay" data-container="replylist_{{$item->id}}"
                          action="/comment/{{$item->id}}/reply"
                          class="form-horizontal form-validation comment_replay" accept-charset="UTF-8" method="post">
                        <div class="textarea">
                            <textarea id="reply_content_{{$item->id}}" @if(!$_user) onClick="Log.logShow();"
                                      @endif name="content"></textarea>
                        </div>
                        <div class="publish clear">
                            <button class="icon_button3" type="submit">回复</button>
                            <span>可以输入<i id="reply_content_limit_{{$item->id}}">{{ $limit }}</i>字</span>
                        </div>
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                    </form>
                    <script type="text/javascript">

                        setTimeout(function () {

                            // attachEventload(function () {
                            $api.textMax({
                                len: '{{ $limit }}',//限制输入的字符个数
                                showId: "#reply_content_limit_{{$item->id}}",//显示剩余字符文本标签的
                                textThis: "#reply_content_{{$item->id}}"
                            });
                            // });

                        }, 2000);

                    </script>
                </div>
            </div>


            <div class="comment_form" id="replylist_{{$item->id}}">
                @if(count($item->child_data) > 0)

                    @foreach($item->child_data as $reply)
                        <div class="comment_reply_list">
                            <p class="user"><span class="tel">{{$reply->operator_nickname}}</span><span
                                        class="timer">{{showWriteTime(strtotime($reply->created_at))}}</span></p>
                            {{--<p class="ans"> {{$reply->content}}</p>--}}
                            <pre style="white-space: pre-wrap; padding:0 10px 0 30px; position: relative; top: -3px;">{!! $reply->content !!}</pre>
                        </div>
                    @endforeach
                @endif
            </div>


        </div>
    @endforeach

    <div class="ajax_page icon_paging_tow" style="margin-top: 20px;">
        <?php
        $query_data = ['comment_key' => $commentKey, "link_id" => $linkIdStr, 'tpl' => $tpl, 'avgs' => $urlAvgs, 'ajax' => 1, 't' => time()];
        ?>
        <?php if(is_null($list->prev_page_url)): ?>
        <a href="javascript:" class="page">&lt;</a>
        <?php else: ?>
        <?php
        $query_data['avgs']['page'] = $list->current_page - 1;
        $prevUrl = '/comment/list?' . http_build_query($query_data);
        ?>
        <a href="{{$prevUrl}}" class="page">&lt;</a>
        <?php endif; ?>

        <?php for($i = 1;$i <= $list->last_page;$i++):?>
        <?php if($i == $list->current_page):?>
        <a href="javascript:" class="ayes">{{ $i }}</a>
        <?php else: ?>
        <?php
        $query_data['avgs']['page'] = $i;
        $currentUrl = '/comment/list?' . http_build_query($query_data);
        ?>
        <a href="{{$currentUrl}}">{{ $i }}</a>
        <?php endif; ?>
        <?php endfor;?>

        <?php if(is_null($list->next_page_url)): ?>
        <a href="javascript:" class="down">&gt;</a>
        <?php else: ?>
        <?php
        $query_data['avgs']['page'] = $list->current_page + 1;
        $nextUrl = '/comment/list?' . http_build_query($query_data);
        ?>
        <a href="{{$nextUrl}}" class="down">&gt;</a>
        <?php endif; ?>
    </div>
@endif
<script>

    function ajaxfn() {
        $("#comment_{{$commentKey}}_{{$linkIdStr}} .ajax_page a").on("click", function () {
            var _url = $(this).attr('href');
            if ("javascript:" == _url) {
                return;
            }
            layer.load();

            $.get(_url, function (html) {
                layer.closeAll('loading');
                $("#comment_{{$commentKey}}_{{$linkIdStr}}").replaceWith(html);
            });
            return false;
        });
        $("#comment_{{$commentKey}}_{{$linkIdStr}} .comment_Lit .reply_btn a").click(function () {
            $(this).parent().next().toggle();
        });
        $("#comment_{{$commentKey}}_{{$linkIdStr}} .comment_replay").submit(function () {
            var container = $("#" + $(this).data('container'));
            var from = $(this);
            if (!from.find("textarea").val()) {
                layer.msg('请输入内容');
                return false;
            }
            layer.load();
            $api.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    dataType: "json"
                },
                function (data) {
                    if (data.status == 200) {
                        from.find("textarea").val("");
                        container.prepend('<div class="comment_reply_list">\
                                        <p class="user"><span class="tel">' + data.data.operator_nickname + '</span><span class="timer">刚刚</span></p>\
                                        <p class="ans"> ' + data.data.content + '</p>\
                                        </div>\
                                        <div class="line_dashed"></div>');


                        $api.textMax({
                            len: '{{ $limit }}',//限制输入的字符个数
                            showId: "#reply_content_limit_" + data.data.id,//显示剩余字符文本标签的
                            textThis: "#reply_content_" + data.data.id
                        });

                        layer.msg('回复成功');
                    } else {
                        layer.msg(data.msg);
                    }
                    layer.closeAll('loading');
                });
            return false;
        });
    }

    if (typeof $ == "undefined") {
        setTimeout(function () {
            ajaxfn();
        }, 1000);
    } else {
        ajaxfn();
    }
</script>