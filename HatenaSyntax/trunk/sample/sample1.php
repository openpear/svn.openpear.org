<?php
include_once dirname(__FILE__) . '/../test/t/t.php';


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

|*table header |*table header2 |
|apple         |1              |
|orange        |2              |

>|
hoge
fuga|<


>>
***blockquote header
fuga
<<

>|php|
echo "hogehoge";||<

[http://google.com]
';

echo HatenaSyntax::render($str);
/*
<div class="section">
  <h1>header1</h1>

  <h2>header2</h2>

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

  <p>paragraph(<a href="#sec49aa86912d059_footnote_1" name="sec49aa86912d059_1" title="footnote">*1</a>)</p>

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
fuga
  </pre>
  <br>
  <blockquote>
    <h3>blockquote header</h3>
    <p>fuga</p>
  </blockquote>

  <pre class="superpre php">
echo &quot;hogehoge&quot;;
  </pre>

  <p><a href="http://google.com">http://google.com</a></p>
</div>


<div class="footnote">
  <p><a href="#sec49aa86912d059_1" name="sec49aa86912d059_footntoe_1">*1</a>: footnote</p>
</div>
*/
