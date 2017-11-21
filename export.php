<?php

$gistlabels = [];

function isJson($string) {
	json_decode($string);
	return (json_last_error() === JSON_ERROR_NONE);
}

function getGistLabels(array $labels,array $gistlabels) {
	foreach ($labels as $label) {
		/** @var array[] $label */
		foreach ($label['gistIds'] as $id) {
			$gistlabels[$id][]=$label['name'];
		}
	}
	return $gistlabels;
}

/** @noinspection MoreThanThreeArgumentsInspection
 * @param array $gist
 * @param       $export_path
 * @param       $groupname
 * @param array $gistlabels
 */
function exportGist(array $gist, $export_path, $groupname, array $gistlabels) {
	if (count($gist['files'])) {
		/** @var array[] $gist */
		foreach ($gist['files'] as $filedata) {
			echo 'export: '.$filedata['filename']."\n";
			if (!file_exists($export_path.'/'.$groupname)) {
				if (!mkdir($export_path . '/' . $groupname) && !is_dir($export_path . '/' . $groupname)) {
					throw new \RuntimeException(sprintf('Directory "%s" was not created', $export_path . '/' . $groupname));
				}
			}
			$ext = '';
			$path_parts = pathinfo($filedata['filename']);
			if ($path_parts['extension']) {
				$ext = $path_parts['extension'];
			}
			$descr  = '# '.$filedata['filename'] . '  '."\n";
			$descr .= '```'."\n";
			$descr .= $gist['description']."\n";
			$descr .= '```'."\n\n\n";
			$descr .= 'id: `'.$gist['id'] . '`  '."\n";
			if (array_key_exists($gist['id'],$gistlabels)) {
				/** @noinspection PhpIllegalArrayKeyTypeInspection */
				$descr .= 'label: `' . implode('`,`', $gistlabels[$gist['id']]) . "`  \n";
			}
			$descr .= 'createdAt: `'.$gist['createdAt'] . '`  '."\n";
			$descr .= 'updatedAt: `'.$gist['updatedAt'] . '`  '."\n  \n  \n";
			$descr .= '```'.$ext."\n".$filedata['content']."\n\n".'```'."\n\n";
			file_put_contents($export_path.'/'.$groupname.'/'.$filedata['filename'].'.md',$descr);
		}
	}
}

if (count($argv)===1) {
	throw new \RuntimeException('missing gistbox file argument: php export.php yourjsonbackup.json /export-gists-here/');
}

if (count($argv)===2) {
	throw new \RuntimeException('missing export path: php export.php yourjsonbackup.json /export-gists-here/');
}

if (count($argv)===3) {
	if (!file_exists($argv[1])) {
		throw new \RuntimeException('unable to open file: '.$argv[1]);
	}
	if (!file_exists($argv[2])) {
		throw new \RuntimeException('unable to open export path: '.$argv[2]);
	}
}

$jsondstr = file_get_contents($argv[1]);

if (!isJson($jsondstr)) {
	throw new \RuntimeException('the data inside your json file looks like it´s no json');
}

$jsondata = json_decode($jsondstr,TRUE);

if (!is_array($jsondata)) {
	throw new \RuntimeException('something is wrong, we should have an array here');
}

if (array_key_exists('groups', $jsondata)) {
	/** @var array[] $jsondata */
	foreach ($jsondata['groups'] as $group) {
		if (count($group['gists'])) {
			$gistlabels = getGistLabels($group['labels'], $gistlabels);
			/** @var array[] $group */
			foreach ($group['gists'] as $gist) {
				if (!$gist['public']) {
					exportGist($gist, $argv[2], $group['screenname'],$gistlabels);
				}
			}
		} else {
			echo "your json is okay, but there are no gists ??!!\n";
		}
	}
} else {
	throw new \RuntimeException("your json data does not have the structure we are looking for array['groups']");
}