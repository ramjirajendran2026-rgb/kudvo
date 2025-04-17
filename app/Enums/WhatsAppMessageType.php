<?php

namespace App\Enums;

enum WhatsAppMessageType: string
{
    case Text = 'text';
    case Image = 'image';
    case Audio = 'audio';
    case Document = 'document';
    case Video = 'video';
    case Sticker = 'sticker';
    case Location = 'location';
    case Contact = 'contact';
    case Interactive = 'interactive';
    case Template = 'template';
    case Reaction = 'reaction';
}
