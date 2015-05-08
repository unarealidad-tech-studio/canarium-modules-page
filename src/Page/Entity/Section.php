<?php
namespace Page\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceManager;

use Zend\Form\Annotation;

/** 
* @ORM\Entity 
* @ORM\Table(name="page_section")
* @ORM\HasLifecycleCallbacks
*/

class Section {
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Page",inversedBy="section")
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
    protected $title;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;
	
	
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
	
	public function setTitle($i){
		$this->title = $i;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setName($i){
		$this->name = $i;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function setContent($i){
		$this->content = $i;
	}
}