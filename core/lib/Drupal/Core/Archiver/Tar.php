<?php

namespace Drupal\Core\Archiver;

/**
 * Defines an archiver implementation for .tar files.
 */
class Tar implements ArchiverInterface {

  /**
   * The underlying ArchiveTar instance that does the heavy lifting.
   *
   * @var \Drupal\Core\Archiver\ArchiveTar
   */
  protected $tar;

  /**
   * Constructs a Tar object.
   *
   * @param string $file_path
   *   The full system path of the archive to manipulate. Only local files
   *   are supported. If the file does not yet exist, it will be created if
   *   appropriate.
   * @param array $configuration
   *   (Optional) settings to open the archive with the following keys:
   *   - 'compress': Indicates if the 'gzip', 'bz2', or 'lzma2' compression is
   *     required.
   *   - 'buffer_length': Length of the read buffer in bytes.
   *
   * @throws \Drupal\Core\Archiver\ArchiverException
   */
  public function __construct($file_path, array $configuration = []) {
    $compress = $configuration['compress'] ?? NULL;
    $buffer = $configuration['buffer_length'] ?? 512;
    $this->tar = new ArchiveTar($file_path, $compress, $buffer);
  }

  /**
   * {@inheritdoc}
   */
  public function add($file_path) {
    $this->tar->add($file_path);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function remove($file_path) {
    // @todo Archive_Tar doesn't have a remove operation
    // so we'll have to simulate it somehow, probably by
    // creating a new archive with everything but the removed
    // file.

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function extract($path, array $files = []) {
    if ($files) {
      $this->tar->extractList($files, $path, '', FALSE, FALSE);
    }
    else {
      $this->tar->extract($path, FALSE, FALSE);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function listContents() {
    $files = [];
    foreach ($this->tar->listContent() as $file_data) {
      $files[] = $file_data['filename'];
    }
    return $files;
  }

  /**
   * Retrieves the tar engine itself.
   *
   * In some cases it may be necessary to directly access the underlying
   * ArchiveTar object for implementation-specific logic. This is for advanced
   * use only as it is not shared by other implementations of ArchiveInterface.
   *
   * @return ArchiveTar
   *   The ArchiveTar object used by this object.
   */
  public function getArchive() {
    return $this->tar;
  }

}
