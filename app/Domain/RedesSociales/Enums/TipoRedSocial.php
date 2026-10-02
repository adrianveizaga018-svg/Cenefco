<?php

namespace App\Domain\RedesSociales\Enums;

enum TipoRedSocial: string
{
    case Facebook = 'facebook';
    case Twitter = 'twitter';
    case Instagram = 'instagram';
    case LinkedIn = 'linkedin';
    case YouTube = 'youtube';
    case TikTok = 'tiktok';
    case WhatsApp = 'whatsapp';
    case Telegram = 'telegram';
    case Pinterest = 'pinterest';
    case Threads = 'threads';
}
