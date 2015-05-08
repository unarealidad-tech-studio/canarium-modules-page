<?php
namespace Page\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Zend\Form\Annotation;

/** 
* @ORM\Entity
* @ORM\HasLifecycleCallbacks
* @ORM\Table(name="page")
*/
class Page {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $permalink;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $template;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metakeywords;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metadescription;
	
	/**
     * @ORM\Column(type="string")
     */
    protected $status = 'Draft';
	
	/**
	  * @ORM\Column(type="datetime", nullable=true)
	  */
	protected $datecreated = null;
	
	/**
	  * @ORM\Column(type="datetime", nullable=true)
	  */
	protected $datemodified = null;
	
	/**
	 * @ORM\OneToMany(targetEntity="Attachment", mappedBy="page", cascade={"persist"})
	 */
	protected $attachment;
	
	/**
	 * @ORM\OneToMany(targetEntity="Section", mappedBy="page", cascade={"persist"})
	 */
	protected $section;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Form\Entity\Form",inversedBy="pages")
	 * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $form;
	
	public function __construct()
	{
		$this->attachment = new ArrayCollection();
		$this->section = new ArrayCollection();
	}
	
	/**
	* @ORM\PreRemove 
	**/
	public function onPreRemove()
	{
		$dir = "./data/uploads/page/".$this->getId();
		if(is_dir($dir)){
			foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
				$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
			}
			rmdir($dir);
		}
	}
	
	/**
	* @ORM\PrePersist
	**/
	public function onPrePersist()
	{
		$this->datecreated = new \DateTime('now');		
	}
	
	/**
	* @ORM\PostPersist
	**/
	public function onPostPersist()
	{
		$destinationFolder = './data/uploads/page/' . $this->getId();
		if(!file_exists($destinationFolder)){
			if(!mkdir($destinationFolder, 0755, true))
				throw new \Exception("Failed to create upload folders");
		}
		
		$destinationFolder = './data/uploads/page/' . $this->getId();
		if(!file_exists($destinationFolder.'/thumbs')){
			if(!mkdir($destinationFolder.'/thumbs', 0755, true))
				throw new \Exception("Failed to create upload folders");
		}
	}
	
	
	
	/**
	* @ORM\PreUpdate
	**/
	public function onPreUpdate(){
		$this->datemodified = new \DateTime('now');	
	}
	
	/**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * Set metakeyword
     *
     * @param string $metakeyword
     * @return Page
     */
    public function setMetakeywords($metakeywords)
    {
        $this->metakeywords = $metakeywords;

        return $this;
    }

    /**
     * Get metakeyword
     *
     * @return string 
     */
    public function getMetakeywords()
    {
        return $this->metakeywords;
    }

    /**
     * Set metadescription
     *
     * @param string $metadescription
     * @return Page
     */
    public function setMetadescription($metadescription)
    {
        $this->metadescription = $metadescription;

        return $this;
    }

    /**
     * Get metadescription
     *
     * @return string 
     */
    public function getMetadescription()
    {
        return $this->metadescription;
    }

    /**
     * Set live
     *
     * @param integer $live
     * @return Page
     */
    public function setStatus($i)
    {
        $this->status = $i;

        return $this;
    }

    /**
     * Get live
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set datecreated
     *
     * @param \DateTime $datecreated
     * @return Page
     */
    public function setDatecreated($datecreated)
    {
        $this->datecreated = $datecreated;

        return $this;
    }

    /**
     * Get datecreated
     *
     * @return \DateTime 
     */
    public function getDatecreated()
    {
        return $this->datecreated;
    }

    /**
     * Set datemodified
     *
     * @param \DateTime $datemodified
     * @return Page
     */
    public function setDatemodified($datemodified)
    {
        $this->datemodified = $datemodified;

        return $this;
    }

    /**
     * Get datemodified
     *
     * @return \DateTime 
     */
    public function getDatemodified()
    {
        return $this->datemodified;
    }
	
	public function setPermalink($i)
    {
        $this->permalink = $i;

        return $this;
    }
	
    public function getPermalink()
    {
        return $this->permalink;
    }
	
	public function getAttachment(){
		return $this->attachment;	
	}
	
	// return one attachment object
	public function getOneAttachmentBy($i = array())
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq(key($i), $i[key($i)]));
        $result = $this->attachment->matching($criteria);
        return $result[0];
    }
	
	// @TODO return collection of attachments
	public function getAttachmentBy($i = array())
    {
        return false;
    }
	
	public function addAttachment(Collection $ii)
    {
        foreach ($ii as $i) {
			$i->setPage($this);
            $this->attachment->add($i);
        }
    }
	
    public function removeAttachment(Collection $ii)
    {
        foreach ($ii as $i) {
            $i->setPage(null);
            $this->attachment->removeElement($i);
        }
    }
	
	public function getSection(){
		return $this->section;	
	}
	
	// return one attachment object
	public function getOneSectionBy($i = array())
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq(key($i), $i[key($i)]));
        $result = $this->section->matching($criteria);
        return $result[0];
    }
	
	// @TODO return collection of attachments
	public function getSectionBy($i = array())
    {
        return false;
    }
	
	public function addSection(Collection $ii)
    {
        foreach ($ii as $i) {
			$i->setPage($this);
            $this->section->add($i);
        }
    }
	
    public function removeSection(Collection $ii)
    {
        foreach ($ii as $i) {
            $i->setPage(null);
            $this->section->removeElement($i);
        }
    }
	
	public function getTemplate(){
		return $this->template;
	}
	
	public function setTemplate($i){
		$this->template = $i;
	}
	
	public function getForm()
	{
		return $this->form;
	}
	
	public function setForm($i)
	{
		$this->form = $i;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($i)
	{
		$this->name = $i;
	}
	
}