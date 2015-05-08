<?php
namespace Page\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceManager;

use Zend\Form\Annotation;

/** 
* @ORM\Entity 
* @ORM\Table(name="page_attachment")
* @ORM\HasLifecycleCallbacks
*/

class Attachment {
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Page",inversedBy="attachment")
	 * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $page;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tmp_name;
	
	/**
     * @ORM\Column(type="integer", length=255)
     */
	protected $error = 0;
	
	/**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
	protected $size;
	
	/**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
	protected $type;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
	protected $title;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
	protected $description;
	
	public function __construct($service)
    {
        $this->service = $service;
    }
	
	/**
	* @ORM\PostPersist
	* @ORM\PostUpdate
	**/
	public function attachments()
	{
		if(!$this->service) return false;
		$thumbnailer = $this->service->get('WebinoImageThumb');
		$dir = "./data/uploads/page/".$this->getPage()->getId();
		$thumb = $thumbnailer->create( $dir . "/" . $this->getTmpname() , $options = array('jpegQuality' => 90));				
		$fileInfo = pathinfo( $this->getTmpname() );				
		#$thumb->resize(640, 480);
		
		$destinationFolder = './data/uploads/page/' . $this->getPage()->getId();
		if(!file_exists($destinationFolder.'/thumbs')){
			if(!mkdir($destinationFolder.'/thumbs', 0755, true))
				throw new \Exception("Failed to create upload folders");
		}
		
		// Save Thumbnails
		$thumb->save($destinationFolder .'/'.$this->getTmpname());
		$thumb->resize(120, 96);
		$thumb->save($destinationFolder .'/thumbs/'.$this->getTmpname());
	}
	
	/**
	* @ORM\PostRemove
	**/
	public function onPostRemove()
	{
		$dir1 = "./data/uploads/page/".$this->getPage()->getId().'/'.$this->getTmpname();
		$dir2 = "./data/uploads/page/".$this->getPage()->getId().'/thumbs/'.$this->getTmpname();
		if(is_dir($dir1)) unlink($dir1);
		if(is_dir($dir2)) unlink($dir2);
	}
	
    public function getId(){
		return $this->id;
	}
	
	public function setPage($i)
	{
		$this->page = $i;
	}
	
	public function getPage()
	{
		return $this->page;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getTmpname(){
		return $this->tmp_name;
	}
	
	public function setTmpname($tmp_name){
		$this->tmp_name = $tmp_name;
	}
	
	public function getError(){
		return $this->error;
	}
	
	public function setError($error){
		$this->error = $error;
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function setSize($i){
		$this->size = $i;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function setType($i){
		$this->type = $i;
	}
	
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	
	public function getPath()
	{
		return '/page/'.$this->page->getId().'/'.$this->tmp_name;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setTitle($i){
		$this->title = $i;
	}
	
	public function getDescription(){
		return $this->description;
	}
	
	public function setDescription($i){
		$this->description = $i;
	}
}