<?php

namespace QuickCms\SDK\Requests\Comment;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment_key'    => 'required',
            'link_id'    => 'required',
            'content'    => 'required',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        return [
            'comment_key.required'  => '缺少参数信息',
            'link_id.required'  => '缺少参数信息',
            'content.required'  => '评论不可为空',
        ];
    }
}
