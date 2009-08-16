<?php
include_once dirname(__FILE__) . '/../code/HatenaSyntax.php';


$str = '*header1

**header2

:definition term:definition description
::description2

-list1
++fuga2
++hoge3
---list4
---list5
-list6

paragraph((footnote))
[[relativelink]]
[][[nulllink]][]

|*table header |*table header2 |
|apple         |1              |
|orange        |2              |

>|
hoge
fuga|<

[http://example.com/example.gif:image]

>>
***blockquote header
fuga
<<

>|php|
<?php
echo "hogehoge";||<

[http://google.com]
';

function sprehandler($type, Array $lines)
{
    foreach ($lines as &$line) $line = htmlspecialchars($line, ENT_QUOTES, 'utf-8');
    $body = join(PHP_EOL, $lines);
    return '<pre class="superpre ' . htmlspecialchars($type, ENT_QUOTES, 'utf-8') 
           . '">' . PHP_EOL . $body . '</pre>';
}

// オプションは全て省略可。第二引数自体も省略可。
echo HatenaSyntax::render($str, array('headerlevel' => 3,                   // ヘッダの基準値。デフォルトは1
                                      'id' => 'hoge',                       // 記事の識別子。指定しない場合はランダムなIDが生成される
                                      'htmlescape' => false,                // デフォルトはtrue。
                                      'sectionclass' => 'section',          // 記事本体を囲むdiv要素のクラス。デフォルトは'section'
                                      'footnoteclass' => 'footnote',        // 脚注を囲むdiv要素のクラス。デフォルトは'footnote'
                                      'superprehandler' => 'sprehandler')); // superpre記法の中身を処理するコールバック。
/*
<div class="section">
  <h3>header1</h3>

  <h4>header2</h4>

  <dl>
    <dt>definition term</dt>
    <dd>definition description</dd>
    <dd>description2</dd>
  </dl>

  <ul>
    <li>list1</li>
    <ol>
      <li>fuga2</li>
      <li>hoge3</li>
      <ul>
        <li>list4</li>
        <li>list5</li>
      </ul>
    </ol>
    <li>list6</li>
  </ul>

  <p>paragraph(<a href="#hoge_footnote_1" name="hoge_1" title="footnote">*1</a>)</p>
  <p><a href="relativelink">relativelink</a></p>
  <p>[[nulllink]]</p>

  <table>
    <tr>
      <th>table header </th>
      <th>table header2 </th>
    </tr>
    <tr>
      <td>apple         </td>
      <td>1              </td>
    </tr>
    <tr>
      <td>orange        </td>
      <td>2              </td>
    </tr>
  </table>

  <pre>
hoge
fuga</pre>

  <p><a href="http://example.com/example.gif"><img src="http://example.com/example.gif" /></a></p>

  <blockquote>
    <h5>blockquote header</h5>
    <p>fuga</p>
  </blockquote>

<pre class="superpre php">
&lt;?php
echo &quot;hogehoge&quot;;</pre>

  <p><a href="http://google.com">http://google.com</a></p>
</div>


<div class="footnote">
  <p><a href="#hoge_1" name="hoge_footnote_1">*1</a>: footnote</p>
</div>
*/
