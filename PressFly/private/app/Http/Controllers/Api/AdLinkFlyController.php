<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class AdLinkFlyController extends Controller
{
    /** @var int Time out in minutes */
    public int $time_out = 5;

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function articleView(Request $request)
    {
        $data = $request->query('data');

        if (empty($data)) {
            //die( 'Error: No data' );
            die();
        }

        try {
            $output = json_decode(adlinkfly_decrypt(rawurldecode($data)), true);
            //return "<pre>" . print_r( $output, true ) . "</pre>";
        } catch (\Exception $exception) {
            //die( 'Error: Invalid data' );
            die();
        }

        if (time() - $output['time'] > $this->time_out * 60) {
            //die( 'Error: Expired data' );
            die();
        }

        $article_data = $this->get_random_article();

        if ($article_data['id'] === 0) {
            //die('Error: No available articles');
            die();
        }

        $connector = '?';
        if (str_contains($article_data['url'], '?')) {
            $connector = '&';
        }

        $redirect_url = $article_data['url'] . $connector . 'fbclid2=' . rawurlencode($data);
        ?>
        <!DOCTYPE html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="referrer" content="no-referrer">
        </head>
        <body>

        <script type="text/javascript">
            var div = document.createElement("div");
            div.innerHTML = '<iframe id="redirect-frame" sandbox="allow-scripts allow-top-navigation" src="data:text/html;charset=utf-8,<scr\ipt>window.addEventListener(\'message\', function(event){ if(event.origin == \'' + window.origin + '\') top.window.location = event.data; });</scr\ipt>" style="display: none !important;">';

            document.body.appendChild(div);

            window.addEventListener("load", function () {
                if (document.getElementById('redirect-frame')) {
                    document.getElementById('redirect-frame').contentWindow.postMessage('<?php echo $redirect_url; ?>', '*');
                } else {
                    var a = document.createElement('a');

                    a.href = '<?php echo $redirect_url; ?>';
                    a.rel = 'noreferrer';
                    a.id = 'redirect-link';

                    document.body.appendChild(a);

                    a.click();
                }

            });
        </script>

        </body>
        </html>
        <?php
        die();
    }

    protected function get_random_article(): array
    {
        $article = Article::query()
            ->whereIn('status', [1, 4])
            ->inRandomOrder()
            ->first();

        if ($article) {
            return [
                'id' => $article->id,
                'url' => $article->permalink(),
            ];
        }

        return [
            'id' => 0,
            'url' => '',
        ];
    }

    public function articleOut()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die();
        }

        if (empty($_POST['data'])) {
            //die( 'No data' );
            die();
        }

        try {
            $data = json_decode(adlinkfly_decrypt($_POST['data']), true);

            if (time() - $data['time'] < get_option('adlinkfly_counter', 5)) {
                //die( 'Error: Expired data' );
                die();
            }

            if (time() - $data['time'] > $this->time_out * 60) {
                //die( 'Error: Expired data' );
                die();
            }

            $send = json_encode(
                [
                    'short' => $data['short'],
                    'alias' => $data['alias'],
                    'status' => 1,
                    'time' => time(),
                ]
            );
            $send = adlinkfly_encrypt($send);

            $connector = '?';
            if (str_contains($data['short'], '?')) {
                $connector = '&';
            }

            $url = $data['short'] . $connector . 'data=' . rawurlencode($send) . '&type=pressfly';

            //header( "Referrer-Policy: no-referrer" );
            header("Location: $url", true, 302);
        } catch (\Exception $exception) {
            //die($exception->getMessage());
        }

        die();
    }
}
