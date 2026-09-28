<x-layouts.admin title="کارفرمایان">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.clients.create') }}" class="admin-btn admin-btn-primary">+ افزودن کارفرمای جدید</a>
    </div>

    @if($clients->isEmpty())
        <p style="color: var(--a-muted)">هنوز کارفرمایی ثبت نشده است.</p>
    @else
        <div class="admin-card overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ترتیب</th>
                        <th>کارفرما</th>
                        <th>حوزه فعالیت</th>
                        <th>وضعیت</th>
                        <th>ویژه</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td>
                                <div class="flex items-center gap-1">
                                    <form method="POST" action="{{ route('admin.clients.reorder') }}">
                                        @csrf
                                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" class="admin-btn" {{ $loop->first ? 'disabled' : '' }}>▲</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.clients.reorder') }}">
                                        @csrf
                                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" class="admin-btn" {{ $loop->last ? 'disabled' : '' }}>▼</button>
                                    </form>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.clients.edit', $client) }}" class="font-bold hover:underline">{{ $client->name }}</a>
                            </td>
                            <td style="color: var(--a-muted)">{{ $client->industry ?: '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.clients.toggle-published', $client) }}">
                                    @csrf
                                    <button type="submit" class="admin-badge" style="{{ $client->published ? 'border-color: var(--a-success); color: var(--a-success)' : '' }}">
                                        {{ $client->published ? 'منتشرشده' : 'پیش‌نویس' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.clients.toggle-featured', $client) }}">
                                    @csrf
                                    <button type="submit" class="admin-badge" style="{{ $client->featured ? 'border-color: var(--a-primary); color: var(--a-primary)' : '' }}">
                                        {{ $client->featured ? 'ویژه' : '—' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="admin-btn">ویرایش</a>
                                    <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('این کارفرما و تمام نمونه‌کارهای آن حذف شود؟');">
                                        @csrf
                                        <button type="submit" class="admin-btn admin-btn-danger">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.admin>
