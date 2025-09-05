export function truncateWords(text, count = 20) {
    if (!text) return '';
    const plain = String(text).replace(/<[^>]*>/g, '');
    const words = plain.trim().split(/\s+/);
    return words.length > count ? words.slice(0, count).join(' ') + '…' : plain;
}
