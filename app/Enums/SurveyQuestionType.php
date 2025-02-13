<?php

namespace App\Enums;

enum SurveyQuestionType: string
{
    case ShortAnswer = 'short_answer';
    case Paragraph = 'paragraph';
    case MultipleChoice = 'multiple_choice';
    case Checkboxes = 'checkboxes';
    case Date = 'date';
    case Time = 'time';
    case DateTime = 'datetime';
    case FileUpload = 'file_upload';
    case Signature = 'signature';
    case Email = 'email';
    case Phone = 'phone';
    case Number = 'number';
    case Url = 'url';
}
