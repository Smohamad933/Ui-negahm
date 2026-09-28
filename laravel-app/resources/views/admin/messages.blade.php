<x-layouts.admin title="پیام‌های تماس">
    @if($messages->isEmpty())
        <p style="color: var(--a-muted)">هنوز پیامی دریافت نشده است.</p>
    @else
        <div class="admin-card overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>وضعیت</th>
                        <th>نام</th>
                        <th>ایمیل / تلفن</th>
                        <th>موضوع</th>
                        <th>پیام</th>
                        <th>تاریخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                        <tr style="{{ ! $message->is_read ? 'font-weight:700' : 'opacity:.75' }}">
                            <td>
                                <form method="POST" action="{{ route('admin.messages.toggle-read', $message) }}">
                                    @csrf
                                    <button type="submit" class="admin-badge" style="{{ ! $message->is_read ? 'border-color: var(--a-primary); color: var(--a-primary)' : '' }}">
                                        {{ $message->is_read ? 'خوانده‌شده' : 'خوانده‌نشده' }}
                                    </button>
                                </form>
                            </td>
                            <td>{{ $message->name }}</td>
                            <td>
                                <div dir="ltr" class="text-left">{{ $message->email }}</div>
                                @if($message->phone)<div dir="ltr" class="text-left" style="color: var(--a-muted)">{{ $message->phone }}</div>@endif
                            </td>
                            <td>{{ $message->subject ?: '—' }}</td>
                            <td class="max-w-xs">{{ \Illuminate\Support\Str::limit($message->message, 90) }}</td>
                            <td style="color: var(--a-muted); white-space:nowrap">{{ optional($message->created_at)->format('Y/m/d H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('پیام حذف شود؟');">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.admin>
