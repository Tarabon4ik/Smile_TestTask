<?php
/**
 * Controller Delete
 *
 * @category  Smile
 * @package   Smile\Contact
 * @author    Taras Trubaichuk <taras.goglechuk@gmail.com>
 */

namespace Smile\Contact\Controller\Adminhtml\Entity;

use Exception;
use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Smile\Contact\Api\ContactEntityRepositoryInterface;
use Smile\Contact\Api\Data\ContactEntityInterface;
use Smile\Contact\Api\Data\ContactEntityInterfaceFactory;

/**
 * Class Delete
 */
class Delete extends AbstractAction implements HttpPostActionInterface
{
    /**
     * Grid view acl resource
     */
    const SAVE_ACL_RESOURCE = 'Smile_Contact::contact_entity_delete';

    /**
     * ContactEntity Interface Factory
     *
     * @var ContactEntityInterfaceFactory
     */
    protected $contactEntityInterfaceFactory;

    /**
     * ContactEntity Repository Interface
     *
     * @var ContactEntityRepositoryInterface
     */
    protected $contactEntityRepository;

    /**
     * Save constructor
     *
     * @param Context $context
     * @param ContactEntityInterfaceFactory $contactEntityInterfaceFactory
     * @param ContactEntityRepositoryInterface $contactEntityRepository
     */
    public function __construct(
        Context $context,
        ContactEntityInterfaceFactory $contactEntityInterfaceFactory,
        ContactEntityRepositoryInterface $contactEntityRepository
    ) {
        parent::__construct($context);
        $this->contactEntityInterfaceFactory = $contactEntityInterfaceFactory;
        $this->contactEntityRepository = $contactEntityRepository;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     *
     * @throws NotFoundException
     */
    public function execute()
    {
        $model = $this->contactEntityInterfaceFactory->create();
        $data = $this->getRequest()->getParams();
        if (!$this->getRequest()->getParam(ContactEntityInterface::ID)) {
            unset($data[ContactEntityInterface::ID]);
        }
        $model->setData($data);
        try {
            $this->contactEntityRepository->delete($model);
            $this->messageManager->addSuccessMessage(__("You've deleted the contact entity"));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('contact/entity/index', [ContactEntityInterface::ID => $model->getId()]);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(static::SAVE_ACL_RESOURCE);
    }
}
