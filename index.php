<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<?php
    $series = "a110";
    //print_r($_SERVER);
    $req = explode("/", $_SERVER['REQUEST_URI']);
    if ($req[count($req)-1] == '' )
        array_pop($req);

    if(isset($req[1]))
        $series = $req[1];
    if(isset($req[2]))
        $section = $req[2];
    if(isset($req[3]))
        $package = $req[3];
    if(isset($req[4]))
        $task = $req[4];

//print_r("Series: ".$series."\n");
//print_r("Section: ".$section."\n");
//print_r("Package: ".$package."\n");

    if ($series == "a110")
        $text = "100";
    else if ($series == "c200")
        $text = "200/300";
    else if ($series == "a400")
        $text = "400";
    else if ($series == "vten")
        $text = "vten";

    if(isset($_GET['series']))
        $series = $_GET['series'];
    if(isset($_GET['searchon'])) {
        $searchon = $_GET['searchon'];
        if(isset($_GET['keywords']))
            $keywords = $_GET['keywords'];
        else
            $keywords = '';
    }
?>
<title>Networked Media Tank -- 
<?php
if (isset($series) && !isset($section) && !(isset($searchon)))
    print('List of sections in "'.$text.' Series"');
else if (isset($section) && !isset($package) && $section != "allpackages")
    print('Software Packages in "'.$text.' Series", Subsection '.$section);
else if (isset($section) && !isset($package) && $section == "allpackages")
    print('Software Packages in "'.$text.' Series"');
else if (isset($source))
    print('Details of source package in '.$package.' in "'.$text.' Series"');
else if (isset($searchon)) {
    if ($keywords == '')
        print('Error');
    else
        print('Package Search Results -- '.$keywords);
}
else
    print('Details of package '.$package.' in "'.$text.' Series"');
?>
</title>
<link rev="made" href="mailto:webmaster@debian.org">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Author" content="Debian Webmaster, webmaster@debian.org">
<meta name="Description" content="">
<meta name="Keywords" content="Debian,  wheezy">

<link href="/debpkg.css" rel="stylesheet" type="text/css" media="all">

<script src="/packages.js" type="text/javascript"></script>
</head>
<body>
<div id="header">
   <div id="upperheader">

   <div id="logo">
  <!-- very Debian specific use of the logo stuff -->
</div> <!-- end logo -->
</div> <!-- end upperheader -->

<div id="pnavbar">
 &nbsp;&#x2F; Packages

<?php
if (!isset($searchon))
    print('&#x2F; <a href="/'.$series.'/" >'.$text.' Series</a> ');

if (!isset($section) && $package == '' && !isset($searchon))
    print('&#x2F; Index ');
else if (isset($section) && $package == '' && !isset($searchon))
    print('&#x2F; '.$section.' ');
else if (isset($searchon) && $searchon != 'contents')
    print('&#x2F; package search results ');
else if (isset($searchon) && $searchon == 'contents')
    print('&#x2F; package contents search results ');
else
    print('&#x2F; <a href="/'.$series.'/'.$section.'/" >'.$section.'</a> &#x2F; '.$package.' ');
?>

</div> <!-- end navbar -->

</div> <!-- end header -->
<div id="content">
<?php
//if (isset($searchon))
    //$sec = array("a110", "c200", "a400");
//else
    $sec = array($series);

foreach ($sec as $sect) {
    $sections = file_get_contents($sect."/Packages");
    $result = preg_match_all("|Section: (.*)|", $sections, $output);
    if (!isset($searchon))
        $uniq_array = array_unique($output[1]);
    $array = explode("\n\n\n", $sections);
    foreach ($array as $pkgdata) {
        $data_lines = explode("\n", $pkgdata);
        foreach ($data_lines as $data) {
            if ($data != "") {
            if ($data[0] != " " ){
                $tmpdata = explode(": ", $data);
                if ($tmpdata[0] == "Package")
                    $pkg = $tmpdata[1];
                $opkg_data[$pkg][$sect][$tmpdata[0]] = trim($tmpdata[1]);
            }
            else {
                $opkg_data[$pkg][$sect]['Description'] .= trim($data);
            }
        }
    }
    $search = array(".tar",".gz",".bz",".xz",".zip", ".src");
    $spackage = str_replace($search, "", basename($opkg_data[$pkg][$sect]['Source']));
    $opkg_data[$pkg][$sect]['sourcepackage'] = $spackage;
    }
}
ksort($opkg_data);
if (!isset($task)){
    print('<form method="GET" action="/index.php"> <div id="hpacketsearch"> <input type="submit" value="Search"><input type="hidden" name="series" value="'.$series.'">');
    print('<select size="1" name="searchon">');
    print('<option value="names" ');
    if ($searchon == "names")
        print('selected="selected"');
    print('>package names</option>');
    print('<option value="all" ');
    if ($searchon == "all")
        print('selected="selected"');
    print('>descriptions</option>');
    print('<option value="sourcenames" ');
    if ($searchon == "sourcenames")
        print('selected="selected"');
    print('>source package names</option>');
    print('<option value="contents" ');
    if ($searchon == "contents")
        print('selected="selected"');
    print('>package contents</option> </select>');
    print('<input type="text" size="30" name="keywords" value="'.$keywords.'" id="kw">');
    print('</div> <!-- end hpacketsearch --> </form>');

    if (!isset($source)){
        if($series == "a110") 
            print('<div id="pothers">       [&nbsp;    <strong>100 Series</strong>  &nbsp;] [&nbsp;    <a  href="'.str_replace("a110", "c200", $_SERVER['REQUEST_URI']).'">200/300 Series</a>  &nbsp;] [&nbsp;    <a  href="'.str_replace("a110", "a400", $_SERVER['REQUEST_URI']).'">400 Series</a>  &nbsp;] [&nbsp;    <a  href="'.str_replace("a110", "vten", $_SERVER['REQUEST_URI']).'">VTEN Series</a>  &nbsp;]</div>'."\n");
        else if ($series == "c200")
            print('<div id="pothers">       [&nbsp;    <a href="'.str_replace("c200", "a110", $_SERVER['REQUEST_URI']).'">100 Series</a>  &nbsp;] [&nbsp;    <strong>200/300 Series</strong>  &nbsp;] [&nbsp;    <a  href="'.str_replace("c200", "a400", $_SERVER['REQUEST_URI']).'">400 Series</a>  &nbsp;] [&nbsp;    <a  href="'.str_replace("c200", "vten", $_SERVER['REQUEST_URI']).'">VTEN Series</a>  &nbsp;]</div>'."\n");
        else if ($series == "a400")
            print('<div id="pothers">       [&nbsp;    <a href="'.str_replace("a400", "a110", $_SERVER['REQUEST_URI']).'">100 Series</a>  &nbsp;] [&nbsp;    <a  href="'.str_replace("a400", "c200", $_SERVER['REQUEST_URI']).'">200/300 Series</a>  &nbsp;] [&nbsp;    <strong>400 Series</strong>  &nbsp;] [&nbsp;    <a  href="'.str_replace("a400", "vten", $_SERVER['REQUEST_URI']).'">VTEN Series</a>  &nbsp;]</div>'."\n");
        else if ($series == "vten")
            print('<div id="pothers">       [&nbsp;    <a href="'.str_replace("vten", "a110", $_SERVER['REQUEST_URI']).'">100 Series</a>  &nbsp;] [&nbsp;    <a  href="'.str_replace("vten", "c200", $_SERVER['REQUEST_URI']).'">200/300 Series</a>  &nbsp;] [&nbsp;    <a  href="'.str_replace("vten", "vten", $_SERVER['REQUEST_URI']).'">400 Series</a>  &nbsp;] [&nbsp;    <strong>VTEN Series</strong>  &nbsp;]</div>'."\n");

    if (isset($opkg_data[$package][$series]['Package'])) {
        print('<div id="psource">
      [&nbsp;Source:    <a href="/'.$series.'/'.$section.'/'.$package.'/source" title="Source package building this package">'.$opkg_data[$package][$series]['sourcepackage'].'</a>  &nbsp;]
</div>

<!-- messages.tmpl -->

<h1>Package: '.$package.' ('.$opkg_data[$package][$series]['Version'].')
</h1>
<div id="pmoreinfo">
<h2>Links for '.$package.'</h2>
  <h3>Download Source Package '.$package.':</h3>
    <ul>
    <li><a href="'.$opkg_data[$package][$series]['Source'].'">['.basename($opkg_data[$package][$series]['Source']).']</a></li>
    </ul>');
$maintainer = explode(" ", $opkg_data[$package][$series]['Maintainer']);
$email = str_replace(">", "", str_replace("<","", $maintainer[1]));
print('<h3>Maintainer:</h3><ul>	<li><a href="mailto:'.$email.'">'.$maintainer[0].'</a>
	</li></ul>');
if (isset($opkg_data[$package][$series]['HomePage'])) {
$home = explode("/", $opkg_data[$package][$series]['HomePage']);
print('<h3>External Resources:</h3>
<ul>
<li><a href="'.$opkg_data[$package][$series]['HomePage'].'">Homepage</a> ['.$home[2].']</li>
</ul>');
}
print('</div> <!-- end pmoreinfo -->

<div id="ptablist">
</div>


<div id="pdesctab">

<div id="pdesc" >
	
	<h2>'.$opkg_data[$package][$series]['Description'].'</h2>
	<p>'.str_replace("..",".<br/><br/>",$opkg_data[$package][$series]['Description']).'</div> <!-- end pdesc -->

</div> <!-- pdesctab -->

      <div id="pdeps">
    <h2>Other Packages Related to '.$package.'</h2>

    <table id="pdeplegend" class="visual" summary="legend"><tr>
    
    <td><ul class="uldep"><li>depends</li></ul></td>
    <td><ul class="ulrec"><li>recommends</li></ul></td>
    <td><ul class="ulsug"><li>suggests</li></ul></td>
    <td><ul class="ulenh"><li>enhances</li></ul></td>
    
    </tr></table>');

print('<ul class="uldep">');
if(isset($opkg_data[$package][$series]['Depends'])) {
    $depends = explode(", ", $opkg_data[$package][$series]['Depends']);
    for($i=0;$i<count($depends);$i++){
        $orpkg = explode(" | ", $depends[$i]);
        for($k=0;$k<count($orpkg);$k++){
            $count = preg_match("|(.*?) \((.*)\)|", $orpkg[$k], $output);
            if ($count > 0) {
                $version = '('.trim($output[2]).')';
                $depend = trim($output[1]);
            }
            else {
                $version = '';
                $depend = trim($orpkg[$k]);
            }
            if ($k == 0)
                print('<li> <dl> <dt><span class="nonvisual">dep:</span> <a href="/'.$series.'/'.$opkg_data[$depend][$series]['Section'].'/'.$depend.'">'.$depend.'</a> '.$version);
            else
                print('<dt>or <a href="/'.$series.'/'.$opkg_data[$depend][$series]['Section'].'/'.$depend.'">'.$depend.'</a> '.$version);
            print('</dt>        <dd lang="en">'.$opkg_data[$depend][$series]['Description'].'</dd> </dl>');
        }
    }
}
print('</ul>');
print('<ul class="ulrec">');
if(isset($opkg_data[$package][$series]['Recommends'])) {
    $recommends = explode(", ", $opkg_data[$package][$series]['Recommends']);
    for($i=0;$i<count($recommends);$i++){
        $count = preg_match("|(.*?) \((.*)\)|", $recommends[$i], $output);
        if ($count > 0) {
            $version = '('.trim($output[2]).')';
            $recommend = trim($output[1]);
        }
        else {
            $version = '';
            $recommend = trim($recommends[$i]);
        }
        print('<li> <dl> <dt><span class="nonvisual">rec:</span> <a href="/'.$series.'/'.$opkg_data[$recommend][$series]['Section'].'/'.$recommend.'">'.$recommend.'</a> '.$version);
        print('</dt>        <dd lang="en">'.$opkg_data[$recommend][$series]['Description'].'</dd> </dl>');
    }
}
print('</ul>');
print('<ul class="ulsug">');
if(isset($opkg_data[$package][$series]['Suggests'])) {
    $suggests = explode(", ", $opkg_data[$package][$series]['Suggests']);
    for($i=0;$i<count($suggests);$i++){
        $count = preg_match("|(.*?) \((.*)\)|", $suggests[$i], $output);
        if ($count > 0) {
            $version = '('.trim($output[2]).')';
            $suggest = trim($output[1]);
        }
        else {
            $version = '';
            $suggest = trim($suggests[$i]);
        }
        print('<li> <dl> <dt><span class="nonvisual">sug:</span> <a href="/'.$series.'/'.$opkg_data[$suggest][$series]['Section'].'/'.$suggest.'">'.$suggest.'</a> '.$version);
        print('</dt>        <dd lang="en">'.$opkg_data[$suggest][$series]['Description'].'</dd> </dl>');
    }
}
print('</ul>
      </div> <!-- end pdeps -->

      <div id="pdownload">
    <h2>Download '.$package.'</h2>
    
    <table summary="The download table links to the download of the package and a file overview. In addition it gives information about the package size and the installed size.">
    <caption class="hidecss">Download for all available architectures</caption>
    <tr><th>Architecture</th>
	<th>Package Size</th>
	<th>Installed Size</th>
	<th>Files</th>
    </tr>
<tr> 
<th><a href="');
if ($section == "devel")
    print("https://sourceforge.net/projects/".$series."opkgdev/files/".$opkg_data[$package][$series]['Filename']);
else {
    if ($series == "c200" || $series == "a110")
        print("https://sourceforge.net/projects/".$series."ipkg/files/".$opkg_data[$package][$series]['Filename']);
    else
        print("https://sourceforge.net/projects/".$series."opkg/files/".$opkg_data[$package][$series]['Filename']);
}
print('">'.$opkg_data[$package][$series]['Architecture'].'</a></th>
<td class="size">'.number_format(round($opkg_data[$package][$series]['Size']/1024,2)).'&nbsp;kB</td><td class="size">');
if (isset($opkg_data[$package][$series]['Installed-Size']))
    print(number_format(round($opkg_data[$package][$series]['Installed-Size']/1024,2)).'&nbsp;kB</td>');
else
    print('0&nbsp;kB</td>');
print('<td>
  [<a href="'.$package.'/filelist">list of files</a>]
</td>
</tr>
</table>

    </div> <!-- end pdownload -->');
        }
    }
}

if (isset($series) && !isset($section) && !(isset($searchon)))
    print('<h1>List of sections in "'.$text.' Series"</h1> <div id="lefthalfcol"><dl>'."\n");
else if (isset($section) && !isset($package) && $section != "allpackages")
    print('<div id="content"> <h1>Software Packages in "'.$text.'", Subsection '.$section.'</h1><!-- messages.tmpl --> <dl>'."\n");
else if (isset($section) && !isset($package) && $section == "allpackages")
    print('<div id="content"> <h1>Software Packages in "'.$text.'"</h1><!-- messages.tmpl --> <dl>'."\n");
else if (isset($searchon))
    if ($keywords == '')
        print('<h1>Error</h1><!-- messages.tmpl --><div class="perror"> <p>keyword not valid or missing</p> </div></div> <!-- end inner -->');
  

if (isset($series) && !isset($section) && !isset($searchon)) {
  if(in_array("admin", $uniq_array))
    print('<dt><a href="admin/">Administration Utilities</a></dt><dd>Utilities to administer system resources, manage user accounts, etc.</dd>'."\n");

  if(in_array("cli-mono", $uniq_array))
    print('<dt><a href="cli-mono/">Mono/CLI</a></dt><dd>Everything about Mono and the Common Language Infrastructure.</dd>'."\n");

  if(in_array("database", $uniq_array))
    print('<dt><a href="database/">Databases</a></dt><dd>Database Servers and Clients.</dd>'."\n");

  if(in_array('devel', $uniq_array))
    print('<dt><a href="devel/">Development</a></dt><dd>Development utilities, compilers, development environments, libraries, etc.</dd>'."\n");

  if(in_array('doc', $uniq_array))
    print('<dt><a href="doc/">Documentation</a></dt><dd>FAQs, HOWTOs and other documents trying to explain everything related to Debian, and software needed to browse documentation (man, info, etc).</dd>'."\n");

  if (in_array('editors', $uniq_array))
    print('<dt><a href="editors/">Editors</a></dt><dd>Software to edit files. Programming environments.</dd>'."\n");
  
  if (in_array('games', $uniq_array))
  print('<dt><a href="games/">Games</a></dt> <dd>Programs to spend a nice time with after all this setting up.</dd>'."\n");
  
  if (in_array('graphics', $uniq_array))
  print('<dt><a href="graphics/">Graphics</a></dt><dd>Editors, viewers, converters... Everything to become an artist.</dd>'."\n");
  
  if (in_array('httpd', $uniq_array))
  print('<dt><a href="httpd/">Web Servers</a></dt><dd>Web servers and their modules.</dd>'."\n");
  
  if (in_array('interpreters', $uniq_array))
  print('<dt><a href="interpreters/">Interpreters</a></dt><dd>All kind of interpreters for interpreted languages. Macro processors.</dd>'."\n");
  
  if (in_array('java', $uniq_array))
  print('<dt><a href="java/">Java</a></dt><dd>Everything about Java.</dd>'."\n");
  
  if (in_array('kernel', $uniq_array))
  print('<dt><a href="kernel/">Kernels</a></dt> <dd>Operating System Kernels and related modules.</dd>'."\n");
  
  if (in_array('libdevel', $uniq_array))
  print('<dt><a href="libdevel/">Library development</a></dt> <dd>Libraries necessary for developers to write programs that use them.</dd>'."\n");
  
  if (in_array('libs', $uniq_array))
  print('<dt><a href="libs/">Libraries</a></dt> <dd>Libraries to make other programs work. They provide special features to developers.</dd>'."\n");

  if (in_array('misc', $uniq_array))
  print('<dt><a href="misc/">Miscellaneous</a></dt> <dd>Miscellaneous utilities that didn\'t fit well anywhere else.</dd>'."\n");

  print('</dl> </div> <!-- end lefthalfcol --> <div id="righthalfcol"> <dl>  '."\n");

  if (in_array('net', $uniq_array))
  print('<dt><a href="net/">Network</a></dt><dd>Daemons and clients to connect your system to the world.</dd>'."\n");

  if (in_array('ocaml', $uniq_array))
  print('<dt><a href="ocaml/">OCaml</a></dt><dd>Everything about OCaml, an ML language implementation.</dd>'."\n");
  
  if (in_array('perl', $uniq_array))
  print('<dt><a href="perl/">Perl</a></dt><dd>Everything about Perl, an interpreted scripting language.</dd>'."\n");
  
  if (in_array('php', $uniq_array))
  print('<dt><a href="php/">PHP</a></dt><dd>Everything about PHP.</dd>'."\n");
  
  if (in_array('python', $uniq_array))
  print('<dt><a href="python/">Python</a></dt> <dd>Everything about Python, an interpreted, interactive object oriented language.</dd>'."\n");
  
  if (in_array('ruby', $uniq_array))
  print('<dt><a href="ruby/">Ruby</a></dt> <dd>Everything about Ruby, an interpreted object oriented language.</dd>'."\n");
  
  if (in_array('shells', $uniq_array))
  print('<dt><a href="shells/">Shells</a></dt> <dd>Command shells. Friendly user interfaces for beginners.</dd>'."\n");

  if (in_array('sound', $uniq_array))
  print('<dt><a href="sound/">Sound</a></dt> <dd>Utilities to deal with sound: mixers, players, recorders, CD players, etc.</dd>'."\n");

  if (in_array('text', $uniq_array))
  print('<dt><a href="text/">Text Processing</a></dt> <dd>Utilities to format and print text documents.</dd>'."\n");

  if (in_array('utils', $uniq_array))
  print('<dt><a href="utils/">Utilities</a></dt> <dd>Utilities for file/disk manipulation, backup and archive tools, system monitoring, input systems, etc.</dd>'."\n");

  if (in_array('vcs', $uniq_array))
  print('<dt><a href="vcs/">Version Control Systems</a></dt> <dd>Version control systems and related utilities.</dd>'."\n");

  if (in_array('video', $uniq_array))
  print('<dt><a href="video/">Video</a></dt> <dd>Video viewers, editors, recording, streaming.</dd>'."\n");

  if (in_array('web', $uniq_array))
  print('<dt><a href="web/">Web Software</a></dt> <dd>Web servers, browsers, proxies, download tools etc.</dd>'."\n");
  
  if (in_array('x11', $uniq_array))
  print('<dt><a href="x11/">X Window System software</a></dt> <dd>X servers, libraries, fonts, window managers, terminal emulators and many related applications.</dd>'."\n");
  
  print('</dl></div> <p class="psmallcenter"> <a href="allpackages" title="List of all packages">All packages</a> </p> </div> <!-- end inner -->'."\n");
}
else if (isset($section) && !isset($package)) {
    foreach($opkg_data as $arch=>$pch){
        foreach($pch as $key => $value) {
            if ($value['Section'] == $section || $section == "allpackages")
                print("<dt><a href='".$value['Package']."' id='".$value['Package']."'>".$value['Package']."</a> (".$value['Version'].")</dt><dd>".$value['Description']."</dd>\n");
        }
    }
}

if (isset($task) && $task == 'source') {
    if (isset($opkg_data[$package][$series]['Package'])) {
        print('<!-- messages.tmpl -->

<h1>Source Package: '.$opkg_data[$package][$series]['sourcepackage'].' ('.$opkg_data[$package][$series]['Version'].')
</h1>
<div id="pmoreinfo">
<h2>Links for '.$package.'</h2>
  <h3>Download Source Package '.$package.':</h3>
    <ul>
    <li><a href="'.$opkg_data[$package][$series]['Source'].'">['.basename($opkg_data[$package][$series]['Source']).']</a></li>
    </ul>');
$maintainer = explode(" ", $opkg_data[$package][$series]['Maintainer']);
$email = str_replace(">", "", str_replace("<","", $maintainer[1]));
print('<h3>Maintainer:</h3><ul>	<li><a href="mailto:'.$email.'">'.$maintainer[0].'</a>
	</li></ul>');
if (isset($opkg_data[$package][$series]['HomePage'])) {
$home = explode("/", $opkg_data[$package][$series]['HomePage']);
print('<h3>External Resources:</h3>
<ul>
<li><a href="'.$opkg_data[$package][$series]['HomePage'].'">Homepage</a> ['.$home[2].']</li>
</ul>');
}
print('</div> <!-- end pmoreinfo -->

<div id="ptablist">
</div>');

}
print('<div id="pbinaries">The following binary packages are built from this source package:<dl>');
foreach($opkg_data as $arch=>$pch){
foreach($pch as $key=>$value){
    if ($value['sourcepackage'] == $opkg_data[$package][$series]['sourcepackage'] && $value['sourcepackage'] != '') {
        print("<dt><a href='/".$series.'/'.$section.'/'.$value['Package']."' id='".$value['Package']."'>".$value['Package']."</a> (".$value['Version'].")</dt><dd>".$value['Description']."</dd>\n");
    }
}
}
print('</dl></div>');
}

if (isset($task) && $task == 'filelist') {
    print('<div id="content"> <h1>Filelist of package <em>'.$package.'</em> in <em>'.$text.' Series</em> of architecture <em>'.$opkg_data[$package][$series]['Architecture'].'</em></h1><!-- messages.tmpl --> <div id="pfilelist"><pre>');
    $command = "cat ".$series."/Packages.filelist ".$series."-dev/Packages.filelist|grep '".$package."'";
    $pkglist1 = explode("\n",trim(shell_exec($command)));
    for($i=0;$i<count($pkglist1);$i++){
        $file = explode(" ", $pkglist1[$i]);
        $file = explode(",", $file[1]);
        for($j=0;$j<count($file);$j++){
            $ss = ":".$opkg_data[$package][$series]['Architecture'].":";
            $ff = explode($ss, $file[$j]);
            if ($ff[0] == $package)
            $outarray[] = "/share/Apps/local".substr($ff[1], 1);
        }
    }
    sort($outarray);
    foreach($outarray as $out) print($out."\n");
    print('</pre></div></div>');
}

if (isset($searchon) && $keywords != '') {
    if ($searchon == "names" || $searchon == "all") {
        foreach($opkg_data as $arch=>$pch){
            foreach($pch as $key=>$value){
                if ($searchon == "names")
                    $pos = stripos($value['Package'], $keywords);
                else {
                    $pos = stripos($value['Package'], $keywords);
                    if ($pos === false) {
                        $pos = stripos($value['Description'], $keywords);
                        if ($pos === false) {
                            $pos = stripos($value['LongDescription'], $keywords);
                        }
                    }
                }
                if ($pos !== false)
                    $res_array[$value['Package']][$key] = $value;
            }
        }
        if ($searchon == "names")
            $txt = "packages that names contain <em>".$keywords."</em> ";
        else
            $txt = "<em>".$keywords."</em> in packages names and descriptions ";
    }
    else if ($searchon == "sourcenames"){
        foreach($opkg_data as $arch=>$pch){
            foreach($pch as $key=>$value){
                $pos = stripos($value['sourcepackage'], $keywords);
                if ($pos !== false)
                    $res_array[$value['sourcepackage']][$key][] = $value;
            }
        }
        $txt = "source packages that names contain <em>".$keywords."</em> ";
    }
    else if ($searchon == "contents") {
        $command = "cat ".$series."/Packages.filelist ".$series."-dev/Packages.filelist|grep '".$keywords." '";
        $pkglist1 = explode("\n",trim(shell_exec($command)));
        for($i=0;$i<count($pkglist1);$i++){
            $file1 = explode(" ", $pkglist1[$i]);
            $file2 = explode(",", $file1[1]);
            for($j=0;$j<count($file2);$j++){
                $ff = explode(":", $file2[$j]);
                $res_array[] = $ff[0]."#/share/Apps/local".substr($ff[2], 1);
            }
        }
        sort($res_array);
        $txt = "paths that end with <em>".$keywords."</em>";
    }
print('<div id="psearchres"> 
<p>You have searched for '.$txt.' in all sections and all architectures.  Found <strong>'.count($res_array).'</strong> matching packages.</p>');

if ($searchon == "names" || $searchon == "all"){
    foreach($res_array as $key => $value){
        print('<h3>Package '.$key.'</h3> <ul> ');
        foreach ($value as $arch => $pch) {
            print('<li class="squeeze"><a class="resultlink" href="/'.$arch.'/'.$pch['Section'].'/'.$pch['Package'].'">'.$arch.'</a> ('.$pch['Section'].'): '.$pch['Description'].'	<br>'.$pch['Version'].': '.$pch['Architecture'].'</li>');
        }
    print('</ul>');
    }
}
else if ($searchon == "sourcenames"){
    foreach($res_array as $key => $value){
        print('<h3>Source Package '.$key.'</h3><ul>');
        foreach($value as $arch => $pch){
            print('<li><a class="resultlink" href="/'.$arch.'/'.$pch[0]['Section'].'/'.$pch[0]['Package'].'/source">'.$arch.'</a> ('.$pch[0]['Section'].'): '.$pch[0]['Version'].'<br>Binary packages: <span id="js_apng2gifwheezyus" class="p_js_elem"></span> <span id="html_apng2gifwheezyus" class="binaries">');
            for($x=0;$x<count($pch)-1;$x++)
              print('<a href="/'.$arch.'/'.$pch[$x]['Section'].'/'.$pch[$x]['Package'].'">'.$pch[$x]['Package'].'</a>, ');
            print('<a href="/'.$arch.'/'.$pch[$x]['Section'].'/'.$pch[count($pch)-1]['Package'].'">'.$pch[count($pch)-1]['Package'].'</a>');
        print('</span></li>');
        }
        print('</span></li></ul>');
    }
    print('</div>');
}
else if ($searchon == "contents"){
    if (count($res_array) > 0) {
print('<table><colgroup><col><col></colgroup><tr><th>File</th><th>Packages</th></tr>');
        foreach($res_array as $out){
print('<tr>');
            $value = explode("#", $out);
            print('<td class="file">'.$value[1].'</td><td><a href="/'.$series.'/'.$opkg_data[$value[0]][$series]['Section'].'/'.$value[0].'">'.$value[0].'</a></td>'."\n");
print('</tr>');
        }
print('<tr><th>File</th><th>Packages</th></tr></table></div>');
    }
}
}
?>

<div id="footer">


<hr class="hidecss">
<hr class="hidecss">
<div id="fineprint" class="bordertop">
<p>
Content Copyright &copy; 2013 - 2014;</p>

</div> <!-- end fineprint -->
</div> <!-- end footer -->
</body>
</html>
