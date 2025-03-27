<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i|after_or_equal:clock_in',
            'break_time.start.*' => 'required|date_format:H:i|after_or_equal:clock_in|before_or_equal:clock_out',
            'break_time.end.*' => [
                'required',
                'date_format:H:i',
                'after_or_equal:clock_in',
                'before_or_equal:clock_out',
                //休憩終了時間が休憩開始時間より後かどうか
                function ($attribute, $value, $fail) {
                    // `break_time.end.*` の `*` の部分（breakTimeId）を取得
                    preg_match('/\d+/', $attribute, $matches);
                    $breakTimeId = $matches[0] ?? null;

                    if ($breakTimeId !== null) {
                        // `break_time.start.{breakTimeId}` の値を取得
                        $startTime = request()->input("break_time.start.{$breakTimeId}");

                        // 終了時間が開始時間より前または同じ場合はエラー
                        if ($startTime && $value < $startTime) {
                            $fail("休憩終了時間 ({$value}) が開始時間 ({$startTime}) より後でなければなりません。");
                        }
                    }
                }
            ],
            'note' => 'required|max:255',
        ];
    }

    public function messages(){
        return [
            'clock_in.required' => '出勤時間を入力してください。',
            'clock_in.date_format' => '出勤時間は「00:00」形式で入力してください。',
            'clock_out.required' => '退勤時間を入力してください。',
            'clock_out.date_format' => '退勤時間は「00:00」形式で入力してください。',
            'clock_out.after_or_equal' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_time.start.*.required' => '休憩開始時間を入力してください。',
            'break_time.start.*.date_format' => '休憩開始時間は「00:00」形式で入力してください。',
            'break_time.start.*.after_or_equal' => '休憩時間が勤務時間外です',
            'break_time.start.*.before_or_equal' => '休憩時間が勤務時間外です',
            'break_time.end.*.required' => '休憩終了時間を入力してください。',
            'break_time.end.*.date_format' => '休憩終了時間は「00:00」形式で入力してください。',
            'break_time.end.*.before_or_equal' => '休憩時間が勤務時間外です',
            'break_time.end.*.after_or_equal' => '休憩時間が勤務時間外です',
            'note.required' => '備考を記入してください',
            'note.max' => '備考は255文字以内で入力してください。',
        ];
    }
}
