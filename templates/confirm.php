<div class="eddditor-modal-confirm">
    <div class="eddditor-modal-confirm-message">
        <p>
            {{ confirm.message }}
        </p>
    </div>
    <div class="eddditor-modal-confirm-buttons">
        <span class="button button-danger button-large" ng-click="confirm.okAction()">{{ confirm.okText }}</span>
        <span class="button button-large" ng-click="confirm.cancelAction()">{{ confirm.cancelText }}</span>
    </div>
</div>