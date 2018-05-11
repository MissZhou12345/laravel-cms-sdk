<?php

namespace QuickCms\SDK\Requests\Comment;

use App\Http\Requests\Request;

class ReplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            'content.required'  => '请输入内容',
        ];
    }
}
