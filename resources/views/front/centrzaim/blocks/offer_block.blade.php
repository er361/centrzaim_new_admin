<div class="offer bordered-block">
    <div class="offer__header">
        <div class="offer__logo" style="display: flex;width: auto;height: 40px;margin-left: auto;margin-right: auto;">
            <img style="width: auto;max-width: 100%" src="{{ data_get($offer, 'logo') }}" alt="{{ data_get($offer, 'site_name') }}">
        </div>
    </div>
    <div class="offer__rating">{{ data_get($offer, 'rating') }}</div>
    <div class="offer__info">
        <div class="offer__line">
            <span class="offer__option">Сумма</span>
            <span class="offer__value">{{ number_format($offer['sum'], 0, ' ', ' ') }} ₽</span>
        </div>
        <div class="offer__line">
            <span class="offer__option">Ставка</span>
            <span class="offer__value">от {{ data_get($offer, 'extendedFields.Условия кредитования.Проценты по кредиту') }}%</span>
        </div>
        @if (data_get($offer, 'extendedFields.Условия кредитования.Срок для микро и кред. карт'))
            <div class="offer__line">
                <span class="offer__option">Срок займа</span>
                <span class="offer__value">{{ data_get($offer['day'], 1, data_get($offer['day'], 0)) }} дней</span>
            </div>
        @endif
    </div>
    @if (data_get($offer, 'extendedFields.Лицензии.Лицензия (N c датой)'))
        <p class="offer__license">Лицензия №{{ data_get($offer, 'extendedFields.Лицензии.Лицензия (N c датой)') }}</p>
    @endif
    <a
            href="{{ data_get($offer, 'offerUrl') }}"
            class="offer__btn btn"
            target="_blank"
            data-redirect="yes"
    >Получить деньги</a>
</div>
