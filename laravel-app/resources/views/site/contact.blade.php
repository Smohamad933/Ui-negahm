<x-layouts.site title="تماس با ما | {{ $settings->site_name }}">

    <section class="container-px pt-6 pb-24">
        <span class="eyebrow">تماس با ما</span>
        <h1 class="h-hero font-display mt-6 max-w-3xl">حرف بزنیم؛ ایده‌هامون رو کنار هم بذاریم.</h1>

        <div class="grid md:grid-cols-[1.1fr_0.9fr] gap-12 mt-16">
            <div class="client-card p-8 md:p-10 reveal-up">
                @if(session('contact_success'))
                    <div class="rounded-2xl p-5 mb-8 font-display font-bold" style="background: var(--color-accent); border:2.5px solid var(--color-fg)">
                        پیام شما با موفقیت ارسال شد؛ به‌زودی با شما تماس می‌گیریم. ✦
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="flex flex-col gap-5">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="نام و نام‌خانوادگی" class="input-field">
                            @error('name')<span class="text-xs mt-2 block" style="color:var(--color-primary)">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="ایمیل" dir="ltr" class="input-field">
                            @error('email')<span class="text-xs mt-2 block" style="color:var(--color-primary)">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="شماره تماس (اختیاری)" dir="ltr" class="input-field">
                        <input type="text" name="subject" value="{{ old('subject') }}" placeholder="موضوع (اختیاری)" class="input-field">
                    </div>
                    <div>
                        <textarea name="message" rows="6" placeholder="پیام شما" class="input-field">{{ old('message') }}</textarea>
                        @error('message')<span class="text-xs mt-2 block" style="color:var(--color-primary)">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn-pill btn-solid self-start">ارسال پیام ↗</button>
                </form>
            </div>

            <div class="flex flex-col gap-6">
                <div class="client-card p-8 reveal-up">
                    <span class="eyebrow">آدرس</span>
                    <p class="mt-4 text-lg font-display font-bold">{{ $settings->contact_address }}</p>
                </div>
                <div class="client-card p-8 reveal-up">
                    <span class="eyebrow">تلفن</span>
                    <p class="mt-4 text-lg font-display font-bold" dir="ltr">{{ $settings->contact_phone }}</p>
                </div>
                <div class="client-card p-8 reveal-up">
                    <span class="eyebrow">ایمیل</span>
                    <p class="mt-4 text-lg font-display font-bold" dir="ltr">{{ $settings->contact_email }}</p>
                </div>
                @if($settings->contact_map_embed)
                    <div class="rounded-[28px] overflow-hidden frame-pop reveal-up">
                        {!! $settings->contact_map_embed !!}
                    </div>
                @endif
            </div>
        </div>
    </section>

</x-layouts.site>
