<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;

class HostRequestStatusNotification extends Notification
{
    use Queueable;

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public function __construct(public readonly string $status)
    {
        if (! in_array($status, [self::APPROVED, self::REJECTED], true)) {
            throw new InvalidArgumentException('Trạng thái yêu cầu Host không hợp lệ.');
        }
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === self::APPROVED) {
            return (new MailMessage)
                ->subject('Tài khoản Host của bạn đã được phê duyệt')
                ->greeting("Xin chào {$notifiable->name}!")
                ->line('Yêu cầu đăng ký Host tại Rẻo Cao Journeys của bạn đã được Admin phê duyệt.')
                ->line('Bạn có thể đăng nhập và bắt đầu quản lý tour, booking cùng các hoạt động dành cho Host.')
                ->action('Đăng nhập tài khoản Host', route('host.login'))
                ->line('Vì lý do bảo mật, Rẻo Cao Journeys sẽ không bao giờ yêu cầu bạn cung cấp mật khẩu qua email.')
                ->salutation('Trân trọng, Rẻo Cao Journeys');
        }

        return (new MailMessage)
            ->subject('Kết quả yêu cầu đăng ký Host')
            ->greeting("Xin chào {$notifiable->name}!")
            ->line('Rất tiếc, yêu cầu đăng ký Host tại Rẻo Cao Journeys của bạn chưa được phê duyệt.')
            ->line('Tài khoản của bạn vẫn hoạt động bình thường với vai trò Khách du lịch và có thể tiếp tục đặt tour trên website.')
            ->action('Trở về Rẻo Cao Journeys', route('home'))
            ->line('Nếu cần thêm thông tin, vui lòng liên hệ với quản trị viên của website.')
            ->salutation('Trân trọng, Rẻo Cao Journeys');
    }
}
