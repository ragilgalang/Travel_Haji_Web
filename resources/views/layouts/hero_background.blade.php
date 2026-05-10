<!-- HERO BACKGROUND: Video atau Slideshow Foto -->
@if(!empty($settings['hero_video']))
  {{-- VIDEO HERO --}}
  <div class="hero-video-bg">
    <video
      src="{{ $settings['hero_video'] }}"
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
        $src = optUrl($settings['hero_bg_'.$i] ?? $defaultBgs[$i - 1], 1200);
      @endphp
      <div class="slide {{ $i === 1 ? 'active' : '' }}">
        <img src="{{ $src }}" alt="Hero Background {{ $i }}" width="1200" height="800"
          {!! $i === 1 ? 'fetchpriority="high"' : 'loading="lazy"' !!} decoding="async">
      </div>
    @endfor
  </div>
@endif

<!-- Floating particles effect (Efek debu/bintang beterbangan) -->
<div class="particles" id="particles"></div>
