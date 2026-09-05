export function extractVideoId(plataforma: string, url: string): string | null {
  if (!url) return null;

  if (plataforma === 'youtube') {
    const match = url.match(/(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
    return match ? match[1] : null;
  }

  if (plataforma === 'vimeo') {
    const match = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
    return match ? match[1] : null;
  }

  if (plataforma === 'tiktok') {
    const match = url.match(/tiktok\.com\/@[^/]+\/video\/(\d+)/);
    return match ? match[1] : null;
  }

  return null;
}

export function getYoutubeThumbnail(videoId: string): string {
  return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
}
