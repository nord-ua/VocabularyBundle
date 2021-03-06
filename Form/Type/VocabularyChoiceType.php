<?php

namespace NordUa\VocabularyBundle\Form\Type;


use NordUa\VocabularyBundle\Document\Vocabulary;
use NordUa\VocabularyBundle\Service\VocabularyService;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class VocabularyChoiceType extends ChoiceType
{

  private $cachedChoices = [];

  /** @var VocabularyService */
  protected $vs;

  public function __construct($vs) {
    $this->vs = $vs;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $options['choice_list'] = $this->getChoices($options);

    parent::buildForm($builder, $options);
  }


  /**
   * {@inheritdoc}
   */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $options['choice_list'] = $this->getChoices($options);

    parent::buildView($view, $form, $options);
  }


  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    parent::setDefaultOptions($resolver);

    $resolver->setRequired(['vocabulary']);
  }

  public function getName()
  {
    return "vocabulary_choice";
  }

  /**
   * @param array $options
   * @return ChoiceList
   */
  private function getChoices(array $options)
  {
    $type = $options['vocabulary'];
    if (!isset($this->cachedChoices[$type])) {
      $values = [];
      $labels = [];
      /* @var $doc Vocabulary */
      foreach ((array)$this->vs->getDocs($type) as $doc) {
        $values[] = $doc->getSlug();
        $labels[] = $doc->getValue();
      }

      $this->cachedChoices[$type] = new ChoiceList($values, $labels);
    }

    return $this->cachedChoices[$type];
  }

}
