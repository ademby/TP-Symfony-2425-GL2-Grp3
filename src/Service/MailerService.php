<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private \Twig\Environment $twig,
        private ParameterBagInterface $params,
        private string $defaultSender,
        private string $replyTo
    ) {}

    /**
     * Sends order confirmation email
     */
    public function sendOrderConfirmation($order, $recipient): void
    {
        $email = (new TemplatedEmail())
            ->from($this->defaultSender)
            ->to($recipient)
            ->replyTo($this->replyTo)
            ->subject(sprintf('Order #%d Confirmation', $order->getId()))
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'order' => $order,
                'customer' => $order->getUser()
            ]);

        $this->mailer->send($email);
    }

    /**
     * Generic email with Twig template
     */
    public function sendTemplatedEmail(
        string $template,
        array $context,
        string $recipient,
        string $subject,
        ?string $sender = null
    ): void {
        $email = (new TemplatedEmail())
            ->from($sender ?? $this->defaultSender)
            ->to($recipient)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);
    }

    /**
     * Raw HTML email (fallback)
     */
    public function sendRawEmail(
        string $content,
        string $recipient,
        string $subject,
        ?string $sender = null
    ): void {
        $email = (new Email())
            ->from($sender ?? $this->defaultSender)
            ->to($recipient)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }
}






// USAGE
// // Specific order email
// $mailer->sendOrderConfirmation($order, $user->getEmail());
// 
// // Generic template email
// $mailer->sendTemplatedEmail(
//     'emails/newsletter.html.twig',
//     ['user' => $user],
//     $user->getEmail(),
//     'Monthly Newsletter'
// );
// 
// // Raw HTML email (last resort)
// $mailer->sendRawEmail(
//     '<h1>Test</h1>',
//     'test@example.com',
//     'Raw Test'
// );