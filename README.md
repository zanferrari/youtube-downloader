# youtube-downloader
 A php only youtube parser and downloader

**Zanfi Youtube downloader** is a blazing fast Youtube parser and Audio+Video or Audio only downloader.

It uses only 1 php class without third party software. It makes no use of the Youtube API (so no api key is needed).

Inspired from the (php) Athlon1600/youtube-downloader and, of course, from the youtube-dl and yt-dlp which are built with Python.

I was annoyed by the throttling that Youtube uses when downloading from an adaptive stream. With this class I measured download speeds even better than yt-dlp.

Just before putting this class online, I got this result:

\- Downloaded 5148614 bytes in **0.56019806861877 seconds**

**How to use the class**:

include the class in your php code.

```plaintext
require('ZanfiYouTube.php');
```

initialize the class with a yotube video ID or a youtube video url

```plaintext
$ZanfiYouTube = new ZanfiYouTube('WaEKXGlfYj8');
```

or

```plaintext
$ZanfiYouTube = new ZanfiYouTube('https://www.youtube.com/watch?v=WaEKXGlfYj8');
```

The most important data is stored in:

```plaintext
$ZanfiYouTube->arr_Video_info;
$ZanfiYouTube->arr_Video_formats;
$ZanfiYouTube->arr_Video_adaptive_formats;
$ZanfiYouTube->arr_Video_thumbnails;
```

just print what you need to see:

```plaintext
$ZanfiYouTube->printFormatted($ZanfiYouTube->arr_Video_adaptive_formats);
```

print the array with the best video+audio data

```plaintext
$ZanfiYouTube->printFormatted($ZanfiYouTube->get_best_both());
```

print the array with the best audio data

```plaintext
$ZanfiYouTube->printFormatted($ZanfiYouTube->get_best_audio());
```

Starts download of video+audio

```plaintext
$ZanfiYouTube->download_best_both();
```

Starts download of audio only

```plaintext
$ZanfiYouTube->download_best_audio();
```

shows the elapsed time for download (use it only after a download...)

```plaintext
echo('Downloaded ' . $ZanfiYouTube->downloaded_bytes . ' bytes in ' . $ZanfiYouTube->elapsed_time . ' seconds');
```

⚠️ Legal Disclaimer

This is a proof of concept. This program is for personal use only. Downloading copyrighted material without permission is against [YouTube's terms of services](https://www.youtube.com/static?template=terms). By using this program, you are solely responsible for any copyright violations. We are not responsible for people who attempt to use this program in any way that breaks YouTube's terms of services.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.