function copyLinkToClipboard() {
    const url = $("#https-download-link").text().trim();
    navigator.clipboard.writeText(url).then(function() {
        console.log("Text copied to clipboard");
    }, function() {
        console.error("Failed to copy text to clipboard");
    });
}
