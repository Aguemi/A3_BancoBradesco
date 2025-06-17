
package br.com.unicuritiba.bancocentral.controller;


import br.com.unicuritiba.bancocentral.model.Contract;
import br.com.unicuritiba.bancocentral.service.ContractService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/contracts")
public class ContractController {

    private final ContractService service;

    public ContractController(ContractService service) {
        this.service = service;
    }

    @GetMapping
    public List<Contract> findAll() {
        return service.findAll();
    }

    @GetMapping("/{id}")
    public ResponseEntity<Contract> findById(@PathVariable Long id) {
        return service.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public Contract create(@RequestBody Contract obj) {
        Contract saved = service.save(obj);
        System.out.println("Contrato criado: " + saved);
        return saved;
    }

    @PutMapping("/{id}")
    public ResponseEntity<Contract> update(@PathVariable Long id, @RequestBody Contract obj) {
        return service.findById(id)
                .map(record -> {
                    obj.setId(id);
                    return ResponseEntity.ok().body(service.save(obj));
                })
                .orElse(ResponseEntity.notFound().build());
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> delete(@PathVariable Long id) {
        return service.findById(id)
                .map(record -> {
                    service.delete(id);
                    return ResponseEntity.ok().<Void>build();
                })
                .orElse(ResponseEntity.notFound().build());
    }
}
