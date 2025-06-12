function downloadAPK() {
    const link = document.createElement('a');
    link.href = '/Resources/app/petisign.apk';
    link.download = 'petisign.apk';
    link.style.display = 'none';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}