
package br.com.unicuritiba.bancocentral.service;


import br.com.unicuritiba.bancocentral.model.Installment;
import br.com.unicuritiba.bancocentral.repository.InstallmentRepository;

import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

@Service
public class InstallmentService {
    private final InstallmentRepository repository;

    public InstallmentService(InstallmentRepository repository) {
        this.repository = repository;
    }

    public List<Installment> findAll() {
        return repository.findAll();
    }

    public Optional<Installment> findById(Long id) {
        return repository.findById(id);
    }

    public Installment save(Installment obj) {
        return repository.save(obj);
    }

    public void delete(Long id) {
        repository.deleteById(id);
    }
}
