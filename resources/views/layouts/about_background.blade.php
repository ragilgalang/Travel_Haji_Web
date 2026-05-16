<!-- ANIMATED ABOUT COLLAGE (KUMPULAN GAMBAR TENTANG KAMI) -->

<div class="img-collage reveal-left">
  <div class="collage-img ci-1">
    @php
      $baseHost = request()->getSchemeAndHttpHost();

      // VIDEO: prioritaskan about_video
      $isVideo = !empty($settings['about_video']);

      if ($isVideo) {
          $rawVideoUrl = $settings['about_video'];
          $mediaUrl = str_starts_with($rawVideoUrl, '/') ? $baseHost . $rawVideoUrl : $rawVideoUrl;
      } else {
          $rawImgUrl = $settings['about_image'] ?? 'https://images.unsplash.com/photo-1574120240282-60c4da46edaf';
          $mediaUrl = str_starts_with($rawImgUrl, '/') ? $baseHost . $rawImgUrl : $rawImgUrl;
      }

      // Fallback gambar untuk onerror
      $fallbackImg = !empty($settings['about_image'])
          ? (str_starts_with($settings['about_image'], '/') ? $baseHost . $settings['about_image'] : $settings['about_image'])
          : 'https://images.unsplash.com/photo-1574120240282-60c4da46edaf';
    @endphp

    @if($isVideo)
      <video autoplay muted loop playsinline preload="auto"
             class="about-video-fit"
             style="width:100%;height:100%;object-fit:cover;border-radius:1.5rem;background:#1e293b;"
             onerror="this.style.display='none'; document.getElementById('about-img-fallback').style.display='block';">
        <source src="{{ $mediaUrl }}" type="video/mp4">
        <source src="{{ $mediaUrl }}" type="video/webm">
      </video>
      {{-- Fallback gambar jika video gagal --}}
      <img id="about-img-fallback" src="{{ $fallbackImg }}" alt="Tentang Kami"
           style="display:none;width:100%;height:100%;object-fit:cover;border-radius:1.5rem;">
    @else
      <img src="{{ optUrl($mediaUrl, 800) }}" alt="Tentang Kami" id="sync-about-img"
           @if(request()->has('preview')) onclick="document.getElementById('about-img-picker').click()" class="pointer"
           @else onclick="openAboutLightbox(this.src)" @endif
           loading="lazy" width="800" height="600"
           style="width:100%;height:100%;object-fit:cover;border-radius:1.5rem;">

      @if(request()->has('preview'))
        <input type="file" id="about-img-picker" style="display:none" accept="image/*"
               onchange="if(window.handleImagePick) handleImagePick(this, '#sync-about-img', 'about_image')">
      @endif
    @endif
  </div>
</div>

<!-- LIGHTBOX OVERLAY -->
<div id="about-lightbox" onclick="closeAboutLightbox()">
  <span id="about-lightbox-close" onclick="closeAboutLightbox()">&times;</span>
  <div class="lightbox-content-wrapper" onclick="event.stopPropagation()">
    <img id="about-lightbox-img" src="" alt="Foto" style="display:none;">
    <video id="about-lightbox-video" src="" controls autoplay style="display:none; max-width:90vw; max-height:80vh; border-radius:8px;"></video>
  </div>
</div>

<script>
  // Fungsi umum untuk membuka media (dipakai di About dan Itinerary)
  function openItinMedia(src, type) {
    openAboutLightbox(src, type);
  }

  function openAboutLightbox(src, type = 'image') {
    const lb = document.getElementById('about-lightbox');
    const img = document.getElementById('about-lightbox-img');
    const vid = document.getElementById('about-lightbox-video');
    
    // Reset
    img.style.display = 'none';
    vid.style.display = 'none';
    vid.pause();
    vid.src = '';

    if (type === 'video' || src.match(/\.(mp4|webm|ogg|mov)/i)) {
      vid.src = src;
      vid.style.display = 'block';
    } else {
      img.src = src;
      img.style.display = 'block';
    }

    lb.classList.add('active');
    document.body.classList.add('overflow-hidden');
  }

  function closeAboutLightbox() {
    const vid = document.getElementById('about-lightbox-video');
    vid.pause();
    document.getElementById('about-lightbox').classList.remove('active');
    document.body.classList.remove('overflow-hidden');
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAboutLightbox();
  });
</script>
