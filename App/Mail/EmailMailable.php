<?php

namespace Jiny\Admin\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $fromEmail;
    public $fromName;
    public $toEmail;
    public $trackingToken;
    public $enableTracking;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body, $fromEmail, $fromName, $toEmail, $trackingToken = null, $enableTracking = true)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->toEmail = $toEmail;
        $this->trackingToken = $trackingToken;
        $this->enableTracking = $enableTracking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
            from: new \Illuminate\Mail\Mailables\Address($this->fromEmail, $this->fromName),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $htmlContent = $this->body;
        
        // 트래킹 활성화 시 픽셀 추가 및 링크 변환
        if ($this->enableTracking && $this->trackingToken) {
            $htmlContent = $this->addTrackingElements($htmlContent);
        }
        
        return new Content(
            htmlString: $htmlContent,
        );
    }
    
    /**
     * 트래킹 요소 추가
     */
    protected function addTrackingElements($html)
    {
        // 1. 트래킹 픽셀 추가
        $trackingPixel = sprintf(
            '<img src="%s" width="1" height="1" style="display:none;" alt="" />',
            route('admin.email.tracking.pixel', ['token' => $this->trackingToken])
        );
        
        // </body> 태그 직전에 픽셀 삽입
        if (stripos($html, '</body>') !== false) {
            $html = str_ireplace('</body>', $trackingPixel . '</body>', $html);
        } else {
            // </body>가 없으면 끝에 추가
            $html .= $trackingPixel;
        }
        
        // 2. 링크 트래킹 (선택적 - 성능 고려)
        // 모든 <a href> 태그를 트래킹 URL로 변환
        $html = $this->wrapLinksWithTracking($html);
        
        return $html;
    }
    
    /**
     * 링크를 트래킹 URL로 감싸기
     */
    protected function wrapLinksWithTracking($html)
    {
        // 간단한 구현 - 실제로는 더 정교한 파싱 필요
        $linkId = 0;
        
        $pattern = '/<a\s+([^>]*\s)?href=["\']([^"\']+)["\']/i';
        
        $html = preg_replace_callback($pattern, function($matches) use (&$linkId) {
            $linkId++;
            $originalUrl = $matches[2];
            
            // 이미 트래킹 URL이거나, mailto:, tel: 등은 제외
            if (strpos($originalUrl, 'email/tracking') !== false || 
                strpos($originalUrl, 'mailto:') === 0 || 
                strpos($originalUrl, 'tel:') === 0) {
                return $matches[0];
            }
            
            $trackingUrl = route('admin.email.tracking.link', [
                'token' => $this->trackingToken,
                'linkId' => $linkId,
                'url' => urlencode($originalUrl)
            ]);
            
            return '<a ' . ($matches[1] ?? '') . 'href="' . $trackingUrl . '"';
        }, $html);
        
        return $html;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}