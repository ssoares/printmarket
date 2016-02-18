<?php
/**
 * Quota.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * ...
 */
class MOXMAN_Quota_Plugin implements MOXMAN_IPlugin {
	public function init() {
		MOXMAN::getPluginManager()->get("core")->bind("BeforeFileAction", "onBeforeFileAction", $this);
	}

	public function onBeforeFileAction(MOXMAN_Vfs_FileActionEventArgs $args) {
		switch ($args->getAction()) {
			case MOXMAN_Vfs_FileActionEventArgs::DELETE:
				$file = $args->getFile();
				$maxSize = $this->parseSize($file->getConfig()->get("quota.max_size", 0));

				if ($maxSize > 0) {
					$currentSize = MOXMAN::getUserStorage()->get("quota.size", 0);
					MOXMAN::getUserStorage()->put("quota.size", max(0, $currentSize - $file->getSize()));

					if (MOXMAN::getLogger()) {
						MOXMAN::getLogger()->debug("[quota] Removed: " . $file->getPublicPath() . " (" . $this->formatSize($file->getSize()) . ").");
					}
				}
				break;

			case MOXMAN_Vfs_FileActionEventArgs::COPY:
			case MOXMAN_Vfs_FileActionEventArgs::ADD:
				if (!isset($args->getData()->thumb)) {
					$file = $args->getTargetFile();
					if (!$file) {
						$file = $args->getFile();
					}

					$maxSize = $this->parseSize($file->getConfig()->get("quota.max_size", 0));
					if ($maxSize === 0) {
						return;
					}

					if (isset($args->getData()->fileSize)) {
						$fileSize = $args->getData()->fileSize;
					} else {
						$fileSize = 0;
					}

					// Get size of source directory in copy operation
					if ($args->getAction() == MOXMAN_Vfs_FileActionEventArgs::COPY && $file->isDirectory() && $fileSize === 0) {
						$fileSize = $this->getDirectorySize($args->getFile());
					}

					$currentSize = MOXMAN::getUserStorage()->get("quota.size", 0);

					if ($currentSize + $fileSize > $maxSize) {
						throw new MOXMAN_Exception(
							"Quota exceeded when adding file: " . $file->getPublicPath() . " (" .
							$this->formatSize($currentSize + $fileSize) .
							" > " .
							$this->formatSize($maxSize) . ")."
						);
					}

					MOXMAN::getUserStorage()->put("quota.size", $currentSize + $fileSize);

					if (MOXMAN::getLogger()) {
						MOXMAN::getLogger()->debug("[quota] Added: " . $file->getPublicPath() . " (" . $this->formatSize($fileSize) . ").");
					}
				}
				break;
		}
	}

	private function getDirectorySize($file) {
		$size = 0;
		$files = $file->listFiles();

		foreach ($files as $file) {
			if ($file->isFile()) {
				$size += $file->getSize();
			} else {
				$size += $this->getDirectorySize($file);
			}
		}

		return $size;
	}

	// @codeCoverageIgnoreStart

	private function parseSize($size) {
		$bytes = floatval(preg_replace('/[^0-9\\.]/', "", $size));

		if (strpos((strtolower($size)), "k") > 0) {
			$bytes *= 1024;
		}

		if (strpos((strtolower($size)), "m") > 0) {
			$bytes *= (1024 * 1024);
		}

		return $bytes;
	}

	private function formatSize($size) {
		if ($size >= 1048576) {
			return round($size / 1048576, 1) . " MB";
		}

		if ($size >= 1024) {
			return round($size / 1024, 1) . " KB";
		}

		return $size . " b";
	}

	// @codeCoverageIgnoreEnd
}

// Add plugin
MOXMAN::getPluginManager()->add("quota", new MOXMAN_Quota_Plugin());

?>