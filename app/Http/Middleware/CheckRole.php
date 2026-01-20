<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
            // Kiểm tra nếu chưa đăng nhập -> đá về login
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // Ví dụ: role 1 là Admin. 
        // Nếu yêu cầu quyền 'admin' mà user không phải id 1 -> Chặn ngay
        if ($role == 'admin' && $user->role_id != 1) {
            abort(403, 'Bạn không có quyền truy cập vào đây!');
        }
        return $next($request);
    }
}
