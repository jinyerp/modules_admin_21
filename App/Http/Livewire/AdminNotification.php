<?php

namespace Jiny\Admin\App\Http\Livewire;

use Livewire\Component;

class AdminNotification extends Component
{
    public $notifications = [];
    
    protected $listeners = [
        'notify' => 'addNotification',
        'notifySuccess' => 'addSuccessNotification',
        'notifyError' => 'addErrorNotification',
        'notifyWarning' => 'addWarningNotification',
        'notifyInfo' => 'addInfoNotification',
    ];
    
    public function mount()
    {
        // 세션에서 플래시 메시지 확인
        if (session()->has('notification')) {
            $this->addNotification(
                session('notification.message'),
                session('notification.type', 'success'),
                session('notification.title', null)
            );
        }
    }
    
    public function addNotification($message, $type = 'success', $title = null)
    {
        $notification = [
            'id' => uniqid(),
            'type' => $type,
            'title' => $title ?: $this->getDefaultTitle($type),
            'message' => $message,
            'show' => false,
        ];
        
        $this->notifications[] = $notification;
        
        // 다음 틱에서 애니메이션 시작
        $this->dispatch('notification-added', ['id' => $notification['id']]);
    }
    
    public function addSuccessNotification($message, $title = null)
    {
        $this->addNotification($message, 'success', $title);
    }
    
    public function addErrorNotification($message, $title = null)
    {
        $this->addNotification($message, 'error', $title);
    }
    
    public function addWarningNotification($message, $title = null)
    {
        $this->addNotification($message, 'warning', $title);
    }
    
    public function addInfoNotification($message, $title = null)
    {
        $this->addNotification($message, 'info', $title);
    }
    
    public function dismissNotification($id)
    {
        $this->notifications = array_filter($this->notifications, function($notification) use ($id) {
            return $notification['id'] !== $id;
        });
        
        $this->notifications = array_values($this->notifications);
    }
    
    private function getDefaultTitle($type)
    {
        return match($type) {
            'success' => '성공',
            'error' => '오류',
            'warning' => '경고',
            'info' => '알림',
            default => '알림',
        };
    }
    
    public function render()
    {
        return view('jiny-admin::livewire.admin-notification');
    }
}