<div class="layotter-modal-confirm">
    <div class="layotter-modal-confirm-message">
        <p>
            {{ confirm.message }}
        </p>
    </div>
    <div class="layotter-modal-confirm-buttons">
        <button type="button" class="button button-danger button-large" ng-click="confirm.okAction()">{{ confirm.okText }}</button>
        <button type="button" class="button button-large" ng-click="confirm.cancelAction()">{{ confirm.cancelText }}</button>
    </div>
</div>