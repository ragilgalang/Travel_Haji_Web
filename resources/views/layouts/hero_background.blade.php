<!-- HERO BACKGROUND: Video atau Slideshow Foto -->
@if(!empty($settings['hero_video']))
  {{-- VIDEO HERO --}}
  @php
    $heroVidUrl = str_starts_with($settings['hero_video'], '/')
        ? request()->getSchemeAndHttpHost() . $settings['hero_video']
        : $settings['hero_video'];
  @endphp
  <div class="hero-video-bg">
    <video
      src="{{ $heroVidUrl }}"
      autoplay muted loop playsinline
      class="hero-video-player"
      style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:0;">
    </video>
    <div class="hero-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.45);z-index:1;"></div>
  </div>
@else
  {{-- SLIDESHOW FOTO --}}
  <div class="hero-slides" id="heroSlides">
    @php
      $defaultBgs = [
        'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=1200&q=70&auto=format,compress&fm=webp&fit=crop',
        'https://images.unsplash.com/photo-1564769625092-b6df1b3e13f0?w=1200&q=70&auto=format,compress&fm=webp&fit=crop',
        'https://images.unsplash.com/photo-1609950547341-a9e24bfeece9?w=1200&q=70&auto=format,compress&fm=webp&fit=crop',
        'https://images.unsplash.com/photo-1466442929976-97f336a657be?w=1200&q=70&auto=format,compress&fm=webp&fit=crop',
      ];
    @endphp

    @for($i = 1; $i <= 4; $i++)
      @php
        $rawBg = $settings['hero_bg_'.$i] ?? null;
        if ($rawBg) {
            // URL lokal dari upload — gunakan asset() bersih
            $cleanBgPath = preg_replace('/^https?:\/\/[^\/]+/', '', $rawBg);
            $cleanBgPath = ltrim($cleanBgPath, '/');
            $src = asset($cleanBgPath);
        } else {
            // Fallback default Unsplash
            $src = $defaultBgs[$i - 1];
        }
      @endphp
      <div class="slide {{ $i === 1 ? 'active' : '' }}">
        <img src="{{ $src }}" alt="Hero Background {{ $i }}" width="1200" height="800"
          {!! $i === 1 ? 'fetchpriority="high"' : 'loading="lazy"' !!} decoding="async"
          style="filter: brightness(0.6) contrast(1.1) saturate(1.2);">
      </div>
    @endfor
    
    {{-- Global Overlay --}}
    <div class="hero-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background: linear-gradient(110deg, rgba(13, 31, 21, 0.95) 0%, rgba(13, 31, 21, 0.75) 45%, rgba(13, 31, 21, 0.5) 100%);z-index:1;"></div>
  </div>
@endif

<!-- Floating particles effect (Efek debu/bintang beterbangan) -->
<div class="particles" id="particles"></div>
